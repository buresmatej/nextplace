<?php

declare(strict_types=1);

namespace App\Components\LoginForm;

use Nette\Application\UI\Control as UiControl;
use Nette\Forms\Form;
use Closure;

class Control extends UiControl
{
    public function __construct(
        private FormFactory $factory,
        private Closure $onSuccess,
    ) {
    }

    public function render(): void
    {
        $this->template->render(__DIR__ . '/default.latte');
    }

    public function createComponentLoginForm(): Form
    {
        $form = $this->factory->create();
        $form->onSuccess[] = $this->onSuccess;

        return $form;
    }
}