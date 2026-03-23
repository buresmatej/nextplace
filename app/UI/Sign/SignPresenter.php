<?php

declare(strict_types=1);

namespace App\UI\Sign;

use App\Components\LoginForm\PresenterTrait as LoginFormPresenterTrait;
use App\Components\RegisterForm\PresenterTrait as RegisterFormPresenterTrait;
use Nette\Application\UI\Presenter;

class SignPresenter extends Presenter
{
    use RegisterFormPresenterTrait;
    use LoginFormPresenterTrait;
    //TODO: acl
}