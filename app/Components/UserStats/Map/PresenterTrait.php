<?php

declare(strict_types=1);

namespace App\Components\UserStats\Map;

use Nette\DI\Attributes\Inject;

trait PresenterTrait
{
    #[Inject]
    public ControlFactory $userStatsMapControlFactory;

    public function createComponentUserStatsMapControl(): Control
    {
        return $this->userStatsMapControlFactory->create();
    }
}