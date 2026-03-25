<?php

declare(strict_types=1);

namespace App\Components\DestinationLogForm;

use Nette\DI\Attributes\Inject;

trait PresenterTrait
{
    #[Inject]
    public ControlFactory $destinationLogFormControlFactory;

    public function createComponentDestinationLogFormControl():  Control
    {
        return $this->destinationLogFormControlFactory->create();
    }

}