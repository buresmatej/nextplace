<?php

declare(strict_types=1);

namespace App\Components\AiRecommendation\Datagrid;

use Nette\DI\Attributes\Inject;

trait PresenterTrait
{
    #[Inject]
    public ControlFactory $aiRecommendationDatagridControlFactory;

    public function createComponentAiRecommendationDatagridControl(): Control
    {
        return $this->aiRecommendationDatagridControlFactory->create();
    }
}