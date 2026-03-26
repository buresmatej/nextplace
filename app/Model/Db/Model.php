<?php

declare(strict_types=1);

namespace App\Model\Db;

use App\Model\Db\Repository\CountryRepository;
use App\Model\Db\Repository\DestinationLogRepository;
use App\Model\Db\Repository\UserRepository;
use Nextras\Orm\Model\Model as NoModel;

/**
 * @property-read UserRepository $userRepository
 * @property-read DestinationLogRepository $destinationLogRepository
 * @property-read CountryRepository $countryRepository
 * */
class Model extends NoModel
{

}