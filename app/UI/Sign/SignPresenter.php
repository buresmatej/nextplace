<?php

declare(strict_types=1);

namespace App\UI\Sign;

use App\Components\LoginForm\PresenterTrait as LoginFormPresenterTrait;
use App\Components\RegisterForm\PresenterTrait as RegisterFormPresenterTrait;
use Nette\Application\UI\Presenter;
use Nette\DI\Attributes\Inject;
use Nette\Security\AuthenticationException;
use Nette\Security\User;

class SignPresenter extends Presenter
{
    use RegisterFormPresenterTrait;
    use LoginFormPresenterTrait;

    #[Inject]
    public User $user;

    public function actionLogin(): void
    {
        if (!$this->user->isAllowed('Sign', 'in')) {
            $this->redirect('Home:default');
        }
    }

    public function actionRegister(): void
    {
        if (!$this->user->isAllowed('Sign', 'up')) {
            $this->redirect('Home:default');
        }
    }

    public function actionOut(): void
    {
        if ($this->user->isAllowed('Sign', 'out')) {
            $this->user->logout();
        }
        $this->redirect('Sign:login');
    }
}