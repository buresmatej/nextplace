<?php

declare(strict_types=1);

namespace App\Components\DestinationLog\Form;

use Closure;
use Nette\Application\UI\Control as UiControl;
use Nette\Forms\Form;

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

    public function createComponentDestinationLogForm(): Form
    {
        $form = $this->factory->create();
        $form->onSuccess[] = $this->onSuccess;

        return $form;
    }
}