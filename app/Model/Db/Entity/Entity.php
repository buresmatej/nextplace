<?php

namespace App\Model\Db\Entity;

use Ramsey\Uuid\Uuid;

/**
 * @property string  $id {primary}
 */
class Entity extends \Nextras\Orm\Entity\Entity
{
    public function __construct()
    {
        parent::__construct();
        $this->id = Uuid::uuid6();
    }
}