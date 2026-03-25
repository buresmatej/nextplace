<?php

declare(strict_types=1);

namespace App\Model\Db\Repository;

use App\Model\Db\Entity\DestinationLog;
use Nextras\Orm\Repository\Repository;
use Override;

class DestinationLogRepository extends Repository
{
    #[Override]
    public static function getEntityClassNames(): array
    {
        return [DestinationLog::class];
    }
}