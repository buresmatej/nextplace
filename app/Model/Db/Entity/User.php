<?php

declare(strict_types=1);


namespace App\Model\Db\Entity;

use DateTimeImmutable;
use Nextras\Orm\Entity\Entity;

/**
 * @property string $id {primary}
 * @property string $username
 * @property string $email
 * @property string $password
 * @property DateTimeImmutable $registrationDate
 */
class User extends Entity
{
}