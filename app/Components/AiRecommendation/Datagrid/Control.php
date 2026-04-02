<?php

declare(strict_types=1);

namespace App\Components\AiRecommendation\Datagrid;

use App\Model\Db\Repository\CountryRepository;
use App\Model\Security\Auth\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
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

    private function loadEnvManually(): void
    {
        // Cesta k .env v rootu projektu (uprav počet ../ podle hloubky tvého adresáře)
        // Předpokládám: app/Components/AiRecommendation/Datagrid/Control.php -> 4 úrovně nahoru
        $envPath = __DIR__ . '/../../../../.env';

        if (file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                // Přeskočit komentáře
                if (strpos(trim($line), '#') === 0) continue;

                // Rozdělit na KLÍČ=HODNOTA
                $parts = explode('=', $line, 2);
                if (count($parts) === 2) {
                    $key = trim($parts[0]);
                    $value = trim($parts[1]);
                    // Nastavit do $_ENV i $_SERVER, pokud tam ještě nejsou
                    if (!isset($_SERVER[$key])) $_SERVER[$key] = $value;
                    if (!isset($_ENV[$key])) $_ENV[$key] = $value;
                }
            }
        }
    }

    public function render(): void
    {
        // Nejdřív zkusíme načíst proměnné ručně
        $this->loadEnvManually();

        $client = new Client();

        $countries = $this->user
            ->getLoggedUser()
            ->destinationLogs
            ->toCollection()
            ->orderBy('rating', ICollection::DESC)
            ->limitBy(10)
            ->fetchPairs('rating', 'country->name');

        $items = [];
        $err = '';

        if (!empty($countries)) {
            $list = implode("\n", array_map(
                fn($name, $rating) => "- {$name}: {$rating}/5",
                $countries,
                array_keys($countries)
            ));

            $prompt = sprintf("You are a travel advisor. The user has visited the following countries and rated them:\n%s\n\nBased on their preferences, recommend 3 countries they should visit next.\nRules:\n- Respond ONLY with ISO 3166-1 alpha-2 country codes\n- Separate them with commas\n- No spaces, no explanation, no markdown, no punctuation, nothing else\n- Output must match exactly this format: XX,XX,XX", $list);

            // Získání proměnných z $_SERVER nebo $_ENV (po manuálním načtení)
            $baseUrl = trim((string)($_SERVER['OPENAI_BASE_URL'] ?? $_ENV['OPENAI_BASE_URL'] ?? ''));
            $apiKey = trim((string)($_SERVER['OPENAI_API_KEY'] ?? $_ENV['OPENAI_API_KEY'] ?? ''));
            $aiModel = trim((string)($_SERVER['AI_MODEL'] ?? $_ENV['AI_MODEL'] ?? ''));

            try {
                if (empty($baseUrl)) {
                    throw new \Exception("Kritická chyba: Konfigurační URL nebyla nalezena. (Zkontroluj cestu k .env v kódu)");
                }

                $response = $client->post($baseUrl, [
                    'headers' => [
                        'Content-Type'  => 'application/json',
                        'Authorization' => 'Bearer ' . $apiKey,
                    ],
                    'json' => [
                        'model'    => $aiModel,
                        'messages' => [
                            ['role' => 'user', 'content' => $prompt],
                        ],
                        'stream'   => false,
                    ],
                ]);

                $data = Json::decode($response->getBody()->getContents(), true);
                $countriesString = $data['choices'][0]['message']['content'] ?? '';
                $countriesString = trim($countriesString);
                $codes = array_map('trim', explode(',', $countriesString));
                $items = $this->countryRepository->findBy(['id' => $codes])->fetchAll();

            } catch (\Exception $e) {
                $err = $e->getMessage();
            }

            // Pro debugování na serveru (pak smaž)
            $this->template->err = $err . ' | Načtená URL: ' . $baseUrl;
        }

        $this->template->items = $items;
        $this->template->render(__DIR__ . '/default.latte');
    }
}