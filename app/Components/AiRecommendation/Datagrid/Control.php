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
        // Podle tvého debugu jsme v /app/www. Soubor .env bude o úroveň výš v /app/.env
        $possiblePaths = [
            '/app/.env',                             // Nejpravděpodobnější cesta na tvém serveru
            dirname(__DIR__, 4) . '/.env',           // Automatický výpočet o 4 úrovně výš
            getcwd() . '/../.env',                   // O úroveň nad aktuálním adresářem (/app/www/../.env)
            '/var/www/.env',
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path) && is_readable($path)) {
                $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                if ($lines === false) continue;

                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line) || str_starts_with($line, '#')) continue;

                    $parts = explode('=', $line, 2);
                    if (count($parts) === 2) {
                        $key = trim($parts[0]);
                        $value = trim($parts[1], "\"' "); // Odstraní uvozovky i mezery

                        $_SERVER[$key] = $value;
                        $_ENV[$key] = $value;
                        putenv("$key=$value");
                    }
                }
                return;
            }
        }
    }

    public function render(): void
    {
        $this->loadEnvManually();
        $client = new Client();
        $items = [];
        $err = '';

        // Zkusíme vytáhnout hodnoty
        $baseUrl = trim((string)($_SERVER['OPENAI_BASE_URL'] ?? $_ENV['OPENAI_BASE_URL'] ?? getenv('OPENAI_BASE_URL') ?: ''));
        $apiKey  = trim((string)($_SERVER['OPENAI_API_KEY'] ?? $_ENV['OPENAI_API_KEY'] ?? getenv('OPENAI_API_KEY') ?: ''));
        $aiModel = trim((string)($_SERVER['AI_MODEL'] ?? $_ENV['AI_MODEL'] ?? getenv('AI_MODEL') ?: ''));

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
                if (empty($baseUrl)) {
                    // Pokud je URL stále prázdná, vypíšeme, kde všude jsme hledali soubor .env
                    throw new \Exception("Chyba: OPENAI_BASE_URL nenalezena. Hledal jsem v /app/.env a dalších.");
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
                    'timeout' => 15,
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

            $this->template->err = $err;
        }

        $this->template->items = $items;
        $this->template->render(__DIR__ . '/default.latte');
    }
}