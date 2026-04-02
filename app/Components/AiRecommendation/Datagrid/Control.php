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

        $baseUrl = trim((string)($_SERVER['OPENAI_BASE_URL'] ?? getenv('OPENAI_BASE_URL') ?: ''));
        $apiKey  = trim((string)($_SERVER['OPENAI_API_KEY'] ?? getenv('OPENAI_API_KEY') ?: ''));
        $aiModel = trim((string)($_SERVER['AI_MODEL'] ?? getenv('AI_MODEL') ?: ''));

        // === DEBUG ===
        $envPath = dirname(__DIR__, 4) . '/.env';
        $debug = [
            'env_file_path'    => $envPath,
            'env_file_exists'  => file_exists($envPath) ? 'ANO' : 'NE',
            'env_file_readable'=> is_readable($envPath) ? 'ANO' : 'NE',
            'sources' => [
                'OPENAI_BASE_URL' => [
                    '$_SERVER'  => $_SERVER['OPENAI_BASE_URL'] ?? '(nenalezeno)',
                    'getenv'    => getenv('OPENAI_BASE_URL') ?: '(nenalezeno)',
                    'vysledek'  => $baseUrl ?: '(PRAZDNE)',
                ],
                'OPENAI_API_KEY' => [
                    '$_SERVER'  => isset($_SERVER['OPENAI_API_KEY'])
                        ? substr($_SERVER['OPENAI_API_KEY'], 0, 6) . '...' : '(nenalezeno)',
                    'getenv'    => getenv('OPENAI_API_KEY')
                        ? substr(getenv('OPENAI_API_KEY'), 0, 6) . '...' : '(nenalezeno)',
                    'vysledek'  => $apiKey ? substr($apiKey, 0, 6) . '...' : '(PRAZDNE)',
                ],
                'AI_MODEL' => [
                    '$_SERVER'  => $_SERVER['AI_MODEL'] ?? '(nenalezeno)',
                    'getenv'    => getenv('AI_MODEL') ?: '(nenalezeno)',
                    'vysledek'  => $aiModel ?: '(PRAZDNE)',
                ],
            ],
            'vsechny_server_keys' => array_filter(
                array_keys($_SERVER),
                fn($k) => str_contains(strtolower($k), 'openai')
                    || str_contains(strtolower($k), 'ai_')
            ),
            'vsechny_getenv' => array_filter([
                'OPENAI_BASE_URL' => getenv('OPENAI_BASE_URL'),
                'OPENAI_API_KEY'  => getenv('OPENAI_API_KEY') ? '(nastaven)' : false,
                'AI_MODEL'        => getenv('AI_MODEL'),
            ]),
        ];
        $debug['getenv_all'] = getenv(); // všechny env vars co PHP zná
        $debug['_ENV']       = $_ENV;    // alternativní superglobal
        // === KONEC DEBUG ===

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
                    throw new \Exception("OPENAI_BASE_URL je prázdná.");
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
        }

        $this->template->debug = $debug;
        $this->template->items = $items;
        $this->template->err   = $err;
        $this->template->render(__DIR__ . '/default.latte');
    }
}