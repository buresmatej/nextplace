<?php

declare(strict_types=1);

namespace App\Components\AiRecommendation\Datagrid;

use App\Model\Db\Repository\CountryRepository;
use App\Model\Security\Auth\User;
use GuzzleHttp\Client;
use Nette\Application\UI\Control as UiControl;
use Nette\Utils\Json;
use Nextras\Orm\Collection\ICollection;

class Control extends UiControl
{
    public function __construct(
        private User $user,
        private CountryRepository $countryRepository,
    ) {
    }

    private function loadEnv(): void
    {
        // Cesta z app/Components/AiRecommendation/Datagrid/ do rootu
        $path = dirname(__DIR__, 4) . '/.env';

        if (file_exists($path)) {
            $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (str_starts_with(trim($line), '#')) continue;
                $parts = explode('=', $line, 2);
                if (count($parts) === 2) {
                    $k = trim($parts[0]);
                    $v = trim($parts[1], "\"' ");
                    if (!isset($_SERVER[$k])) $_SERVER[$k] = $v;
                    putenv("$k=$v");
                }
            }
        }
    }

    public function render(): void
    {
        $this->loadEnv();
        $client = new Client();

        // Zkusíme všechny zdroje najednou
        $baseUrl = trim((string)($_SERVER['OPENAI_BASE_URL'] ?? getenv('OPENAI_BASE_URL') ?: ''));
        $apiKey  = trim((string)($_SERVER['OPENAI_API_KEY'] ?? getenv('OPENAI_API_KEY') ?: ''));
        $aiModel = trim((string)($_SERVER['AI_MODEL'] ?? getenv('AI_MODEL') ?: ''));

        $items = [];
        $err = '';

        $loggedUser = $this->user->getLoggedUser();
        $countries = $loggedUser ? $loggedUser->destinationLogs->toCollection()
            ->orderBy('rating', ICollection::DESC)->limitBy(10)->fetchPairs('rating', 'country->name') : [];

        if (!empty($countries)) {
            $list = implode("\n", array_map(fn($n, $r) => "- $n: $r/5", $countries, array_keys($countries)));
            $prompt = "Recommend 3 countries (ISO codes) based on: \n" . $list;

            try {
                if (!$baseUrl) {
                    throw new \Exception("URL stale prazdna. Zkus v panelu smazat mezery v klici OPENAI_BASE_URL.");
                }

                $response = $client->post($baseUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $apiKey,
                        'Content-Type' => 'application/json'
                    ],
                    'json' => [
                        'model' => $aiModel,
                        'messages' => [['role' => 'user', 'content' => $prompt]],
                        'stream' => false,
                    ],
                    'timeout' => 15
                ]);

                $data = Json::decode($response->getBody()->getContents(), true);
                $resContent = trim($data['choices'][0]['message']['content'] ?? '');
                $codes = array_map('trim', explode(',', $resContent));
                $items = $this->countryRepository->findBy(['id' => $codes])->fetchAll();

            } catch (\Exception $e) {
                $err = $e->getMessage();
            }

            // Pokud to stale nejde, vypiseme klicove info
            if (!$baseUrl) {
                $this->template->err = "Chyba: Promenne nenalezeny. Cesta k env: " . dirname(__DIR__, 4) . '/.env';
            } else {
                $this->template->err = $err;
            }
        }

        $this->template->items = $items;
        $this->template->render(__DIR__ . '/default.latte');
    }
}