<?php

declare(strict_types=1);

namespace App\Components\LoginForm;

use Nette\DI\Attributes\Inject;

trait PresenterTrait
{
    #[Inject]
    public ControlFactory $loginFormControlFactory;

    public function createComponentLoginFormControl():  Control
    {
        return $this->loginFormControlFactory->create();
    }
}