<?php

declare(strict_types=1);

namespace App\Components\DestinationLog\Datagrid;

use App\Model\Db\Repository\DestinationLogRepository;
use App\Model\Security\Auth\User;
use Nette\Application\UI\Control as UiControl;

class Control extends UiControl
{
    public function __construct(
        private DestinationLogRepository $destinationLogRepository,
        private User $user,
    ) {
    }

    public function render(): void
    {
        $this->template->items = $this->destinationLogRepository->findByUser(
            $this->user->getLoggedUser(),
        );
        $this->template->render(__DIR__ . '/default.latte');
    }
}