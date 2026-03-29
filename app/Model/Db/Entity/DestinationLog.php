<?php

declare(strict_types=1);

namespace App\Model\Db\Entity;

use Nextras\Dbal\Utils\DateTimeImmutable;
use Nextras\Orm\Entity\Entity;

/**
 * @property string $id {primary}
 * @property string|null $note
 * @property int $rating
 * @property DateTimeImmutable $createdAt {default 'now'}
 * @property User $user {m:1 User::$destinationLogs}
 * @property Country $country {m:1 Country, oneSided=true}
 */
class DestinationLog extends Entity
{
}