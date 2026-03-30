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

        $countries = $this->user
            ->getLoggedUser()
            ->destinationLogs
            ->toCollection()
            ->orderBy('rating', ICollection::DESC)
            ->limitBy(10)
            ->fetchPairs('rating', 'country->name');
        $items = [];
        if (!empty($countries)) {
            $list = implode("\n", array_map(
                fn($name, $rating) => "- {$name}: {$rating}/5",
                $countries,
                array_keys($countries)
            ));

            $prompt = sprintf('You are a travel advisor. The user has visited the following countries and rated them:\n{$s}\n\nBased on their preferences, recommend 3 countries they should visit next.\nRules:\n- Respond ONLY with ISO 3166-1 alpha-2 country codes\n- Separate them with commas\n- No spaces, no explanation, no markdown, no punctuation, nothing else\n- Output must match exactly this format: XX,XX,XX', $list);

            $response = $client->post('http://ollama:11434/api/generate', [
                'json' => [
                    'model'  => 'gemma3:4b',
                    'prompt' => $prompt,
                    'stream' => false,
                ],
            ]);

            $response = Json::decode($response->getBody()->getContents(), true);
            $countries = trim($response['response']);
            $codes = explode(',', $countries);
            $items = $this->countryRepository->findBy(['id' => $codes])->fetchAll();
        }
        $this->template->items = $items;
        $this->template->render(__DIR__ . '/default.latte');
    }

    //TODO: predelat
}