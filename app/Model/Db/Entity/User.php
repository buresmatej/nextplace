<?php

declare(strict_types=1);


namespace App\Model\Db\Entity;

use DateTimeImmutable;
use Nextras\Orm\Relationships\OneHasMany;

/**
 * @property string $id {primary}
 * @property string $username
 * @property string $email
 * @property string $password
 * @property DateTimeImmutable $registrationDate {default 'now'}
 * @property OneHasMany<DestinationLog> $destinationLogs {1:m DestinationLog::$user}
 */
class User extends Entity
{
}