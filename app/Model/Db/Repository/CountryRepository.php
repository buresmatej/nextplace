<?php

declare(strict_types=1);

namespace App\Model\Db\Repository;

use App\Model\Db\Entity\Country;
use Nextras\Orm\Repository\Repository;
use Override;

class CountryRepository extends Repository
{
    public function findForSelect(): array
    {
        return $this->findAll()
            ->fetchPairs('id', 'name');
    }

    #[Override]
    public static function getEntityClassNames(): array
    {
        return [Country::class];
    }
}