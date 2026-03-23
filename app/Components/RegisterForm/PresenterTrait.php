<?php

declare(strict_types=1);

namespace App\Components\RegisterForm;

use Nette\DI\Attributes\Inject;

trait PresenterTrait
{
    #[Inject]
    public ControlFactory $registerFormControlFactory;

    public function createComponentRegisterFormControl():  Control
    {
        return $this->registerFormControlFactory->create();
    }
}