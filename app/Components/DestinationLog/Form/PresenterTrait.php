<?php

declare(strict_types=1);

namespace App\Components\DestinationLog\Form;

use Nette\DI\Attributes\Inject;

trait PresenterTrait
{
    #[Inject]
    public ControlFactory $destinationLogFormControlFactory;

    public function createComponentDestinationLogFormControl():  Control
    {
        return $this->destinationLogFormControlFactory->create($this->onDestinationLogFormSuccess(...));
    }

    public function onDestinationLogFormSuccess(): void
    {
        $this->redirect('DestinationLog:datagrid');
    }

}