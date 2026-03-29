<?php

declare(strict_types=1);

namespace App\UI\DestinationLog;

use App\Components\DestinationLog\Datagrid\PresenterTrait as DestinationLogDatagridPresenterTrait;
use App\Components\DestinationLog\Form\PresenterTrait as DestinationLogFormPresenterTrait;
use Nette\Application\UI\Presenter;

class DestinationLogPresenter extends Presenter
{
    use DestinationLogFormPresenterTrait;
    use DestinationLogDatagridPresenterTrait;

    public function actionCreate(): void
    {
        if (!$this->getUser()->isAllowed('DestinationLog', 'create')) {
            $this->redirect('Sign:login');
        }
    }

    public function actionDatagrid(): void
    {
        if (!$this->getUser()->isAllowed('DestinationLog', 'seeDatagrid')) {
            $this->redirect('Sign:login');
        }
    }
}