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

    /**
     * Pokusí se najít a načíst .env soubor z nejčastějších umístění v Dockeru.
     */
    private function loadEnvManually(): void
    {
        $possiblePaths = [
            __DIR__ . '/../../../../.env',           // 4 úrovně nahoru (standardní Nette struktura)
            getcwd() . '/.env',                      // Aktuální pracovní adresář
            $_SERVER['DOCUMENT_ROOT'] . '/../.env',  // Nad složkou www/public
            '/var/www/html/.env',                    // Standardní Docker cesta
            '/var/www/.env',                         // Alternativní Docker cesta
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path) && is_readable($path)) {
                $content = file_get_contents($path);
                if ($content === false) continue;

                $lines = explode("\n", $content);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line) || str_starts_with($line, '#')) continue;

                    $parts = explode('=', $line, 2);
                    if (count($parts) === 2) {
                        $key = trim($parts[0]);
                        $value = trim($parts[1]);

                        // Odstranění případných uvozovek kolem hodnoty
                        $value = trim($value, "\"' ");

                        // Naplnění všech možných PHP úložišť
                        $_SERVER[$key] = $value;
                        $_ENV[$key] = $value;
                        putenv("$key=$value");
                    }
                }
                return; // Jakmile jeden najdeme, končíme
            }
        }
    }

    public function render(): void
    {
        // 1. Inicializace a ruční načtení prostředí
        $this->loadEnvManually();
        $client = new Client();
        $items = [];
        $err = '';

        // 2. Načtení proměnných s fallbacky (zkusí $_SERVER, pak $_ENV, pak getenv)
        $baseUrl = trim((string)($_SERVER['OPENAI_BASE_URL'] ?? $_ENV['OPENAI_BASE_URL'] ?? getenv('OPENAI_BASE_URL') ?: ''));
        $apiKey  = trim((string)($_SERVER['OPENAI_API_KEY'] ?? $_ENV['OPENAI_API_KEY'] ?? getenv('OPENAI_API_KEY') ?: ''));
        $aiModel = trim((string)($_SERVER['AI_MODEL'] ?? $_ENV['AI_MODEL'] ?? getenv('AI_MODEL') ?: ''));

        // 3. Logika doporučení
        $loggedUser = $this->user->getLoggedUser();
        $countries = $loggedUser
            ? $loggedUser->destinationLogs->toCollection()
                ->orderBy('rating', ICollection::DESC)
                ->limitBy(10)
                ->fetchPairs('rating', 'country->name')
            : [];

        if (!empty($countries)) {
            $list = implode("\n", array_map(
                fn($name, $rating) => "- {$name}: {$rating}/5",
                $countries,
                array_keys($countries)
            ));

            $prompt = sprintf(
                "You are a travel advisor. The user has visited the following countries and rated them:\n%s\n\nBased on their preferences, recommend 3 countries they should visit next.\nRules:\n- Respond ONLY with ISO 3166-1 alpha-2 country codes\n- Separate them with commas\n- No spaces, no explanation, no markdown, no punctuation, nothing else\n- Output must match exactly this format: XX,XX,XX",
                $list
            );

            try {
                // Kontrola, zda máme URL
                if (empty($baseUrl)) {
                    throw new \Exception("Chyba: OPENAI_BASE_URL je prázdná. Prohledávané cesty: " . getcwd());
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
                    'timeout' => 10, // Timeout pro jistotu
                ]);

                $data = Json::decode($response->getBody()->getContents(), true);
                $countriesString = trim($data['choices'][0]['message']['content'] ?? '');

                if ($countriesString) {
                    $codes = array_map('trim', explode(',', $countriesString));
                    $items = $this->countryRepository->findBy(['id' => $codes])->fetchAll();
                }

            } catch (\Exception $e) {
                $err = "API Error: " . $e->getMessage();
            }

            // Diagnostický výpis (můžeš smazat, až to pojede)
            $this->template->err = $err . " [Debug: URL=" . ($baseUrl ?: 'NULL') . ", Dir=" . getcwd() . "]";
        }

        $this->template->items = $items;
        $this->template->render(__DIR__ . '/default.latte');
    }
}