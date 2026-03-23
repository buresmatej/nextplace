<?php

declare(strict_types=1);

namespace App\Components\LoginForm;

use Nette\Application\UI\Control as UiControl;
use Nette\Forms\Form;

class Control extends UiControl
{
    public function __construct(
        private FormFactory $factory,
    ) {
    }

    public function render(): void
    {
        $this->template->render(__DIR__ . '/default.latte');
    }

    public function createComponentLoginForm(): Form
    {
        return $this->factory->create();
    }
}