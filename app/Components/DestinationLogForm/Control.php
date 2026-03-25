<?php

declare(strict_types=1);

namespace App\Components\DestinationLogForm;

use Nette\Application\UI\Control as UiControl;
use Nette\Forms\Form;
use Closure;

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

    public function createComponentDestinationLogForm(): Form
    {
        $form = $this->factory->create();

        return $form;
    }
}