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

    public function render(): void
    {
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

            $prompt = sprintf('You are a travel advisor. The user has visited the following countries and rated them:\n{$s}\n\nBased on their preferences, recommend 3 countries they should visit next.\nRules:\n- Respond ONLY with ISO 3166-1 alpha-2 country codes\n- Separate them with commas\n- No spaces, no explanation, no markdown, no punctuation, nothing else\n- Output must match exactly this format: XX,XX,XX', $list);
            $baseUrl = $_SERVER['OPENAI_BASE_URL'] ?? $_ENV['OPENAI_BASE_URL'] ?? '';
            $apiKey = $_SERVER['OPENAI_API_KEY'] ?? $_ENV['OPENAI_API_KEY'] ?? '';
            $aiModel = $_SERVER['AI_MODEL'] ?? $_ENV['AI_MODEL'] ?? '';
            try {
                if (!$baseUrl) {
                    throw new \Exception("Kritická chyba: OPENAI_BASE_URL nebyla nalezena v $_SERVER ani v $_ENV");
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
                var_dump(getenv('OPENAI_BASE_URL'));
                var_dump(getenv('OPENAI_API_KEY'));
                var_dump(getenv('AI_MODEL'));

                $data = Json::decode($response->getBody()->getContents(), true);
                $countriesString = $data['choices'][0]['message']['content'] ?? '';
                $countriesString = trim($countriesString);
                $codes = array_map('trim', explode(',', $countriesString));
                $items = $this->countryRepository->findBy(['id' => $codes])->fetchAll();
            } catch (\Exception $e) {
                $err = $e->getMessage();
            }
            $this->template->err = $err . 'promenne: ' . $baseUrl . ' ' . $apiKey . ' ' . $aiModel;
        }
        $this->template->items = $items;
        $this->template->render(__DIR__ . '/default.latte');
    }

    //TODO: predelat
}