<?php

declare(strict_types=1);

namespace App\Model\Db\Entity;

use Nextras\Orm\Entity\Entity;

/**
 * @property string $id {primary}
 * @property string $name
 * @property string $continent
 * @property string $flagEmoji
 */
class Country extends Entity
{
}