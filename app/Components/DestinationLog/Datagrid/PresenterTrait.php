<?php

declare(strict_types=1);

namespace App\Components\DestinationLog\Datagrid;

use Nette\DI\Attributes\Inject;

trait PresenterTrait
{
    #[Inject]
    public ControlFactory $destinationLogDatagridControlFactory;

    public function createComponentDestinationLogDatagridControl(): Control
    {
        return $this->destinationLogDatagridControlFactory->create();
    }
}