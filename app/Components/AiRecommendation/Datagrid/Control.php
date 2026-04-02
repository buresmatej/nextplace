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

    public function render(): void
    {
        $client = new Client();

        // Načtení proměnných z prostředí (priorita $_SERVER, pak getenv)
        $baseUrl = trim((string)($_SERVER['OPENAI_BASE_URL'] ?? getenv('OPENAI_BASE_URL') ?: ''));
        $apiKey  = trim((string)($_SERVER['OPENAI_API_KEY'] ?? getenv('OPENAI_API_KEY') ?: ''));
        $aiModel = trim((string)($_SERVER['AI_MODEL'] ?? getenv('AI_MODEL') ?: ''));

        $items = [];
        $err = '';

        $loggedUser = $this->user->getLoggedUser();

        // Získání historie cest uživatele
        $countries = $loggedUser ? $loggedUser->destinationLogs->toCollection()
            ->orderBy('rating', ICollection::DESC)
            ->limitBy(10)
            ->fetchPairs('rating', 'country->name') : [];

        if (!empty($countries)) {
            $list = implode("\n", array_map(fn($n, $r) => "- $n: $r/5", $countries, array_keys($countries)));

            $prompt = "You are a travel assistant. Based on the user's travel history (country: rating 1-5), recommend 3 NEW countries they haven't visited yet.
            Return ONLY a comma-separated list of ISO 3166-1 alpha-2 country codes.
            DO NOT include any introduction, explanations, formatting or dots.
            
            User travel history:
            " . $list . "
            
            Output format example:
            CZ,SK,AT";

            try {
                if (!$baseUrl) {
                    throw new \Exception("Chybí konfigurace AI (Base URL).");
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
                        'temperature' => 0.3, // Nižší teplota pro striktnější formát
                    ],
                    'timeout' => 15
                ]);

                $data = Json::decode($response->getBody()->getContents(), true);
                $resContent = trim($data['choices'][0]['message']['content'] ?? '');

                // Vyčištění odpovědi od případných teček a rozbití na pole kódů
                $resContent = str_replace([' ', '.'], '', $resContent);
                $codes = array_filter(explode(',', strtoupper($resContent)));

                if (!empty($codes)) {
                    $items = $this->countryRepository->findBy(['id' => $codes])->fetchAll();
                }

            } catch (\Exception $e) {
                $err = "AI doporučení momentálně není dostupné.";
                // Pro vývoj můžeš nechat: $err = $e->getMessage();
            }
        }

        $this->template->items = $items;
        $this->template->err   = $err;
        $this->template->render(__DIR__ . '/default.latte');
    }
}