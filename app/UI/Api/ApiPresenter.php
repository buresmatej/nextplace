<?php

namespace App\UI\Api;

use Nette\Application\UI\Presenter;

class ApiPresenter extends Presenter
{
    public function actionPing(): void
    {
        $this->sendJson(['response' => 'pong!']);
    }

    public function actionStatus(): void
    {
        $this->sendJson([
            'author' => 'https://github.com/buresmatej',
            'time' => new \DateTimeImmutable('now'),
        ]);
    }
}