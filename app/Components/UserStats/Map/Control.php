<?php

declare(strict_types=1);

namespace App\Components\UserStats\Map;

use App\Model\Db\Entity\DestinationLog;
use App\Model\Db\Repository\DestinationLogRepository;
use App\Model\Security\Auth\User;
use Nette\Application\UI\Control as UiControl;


class Control extends UiControl
{
    public function __construct(
        private User $user,
        private DestinationLogRepository $destinationLogRepository,
    ) {
    }

    public function render(): void
    {
        $user = $this->user->getLoggedUser();
        $codes = $this->destinationLogRepository
            ->findByUser($user)
            ->fetchPairs(null, 'country->id');
        $this->template->codes = $codes;
        $this->template->render(__DIR__ . '/default.latte');
    }
}