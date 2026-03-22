<?php

declare(strict_types=1);

namespace App\Model\Db;

use App\Model\Db\Repository\UserRepository;
use Nextras\Orm\Model\Model as NoModel;

/**
 * @property-read UserRepository $userRepository
 */
class Model extends NoModel
{

}