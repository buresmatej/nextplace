<?php

namespace App\UI\User;

use App\Components\UserStats\Map\PresenterTrait as UserStatsMapPresenterTrait;
use Nette\Application\UI\Presenter;

class UserPresenter extends Presenter
{
    use UserStatsMapPresenterTrait;

    public function actionStats(): void
    {
        if (!$this->user->isAllowed('User', 'seeStats')) {
            $this->redirect('Sign:login');
        }
    }
}