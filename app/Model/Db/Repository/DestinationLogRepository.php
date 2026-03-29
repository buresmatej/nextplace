<?php

declare(strict_types=1);

namespace App\Model\Db\Repository;

use App\Model\Db\Entity\DestinationLog;
use App\Model\Db\Entity\User;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Repository\Repository;
use Override;

class DestinationLogRepository extends Repository
{
    public function findByUser(User $user): ICollection
    {
        return $this->findBy([
            'user' => $user,
        ]);
    }

    #[Override]
    public static function getEntityClassNames(): array
    {
        return [DestinationLog::class];
    }
}