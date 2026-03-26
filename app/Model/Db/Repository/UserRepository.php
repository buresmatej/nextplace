<?php

declare(strict_types=1);

namespace App\Model\Db\Repository;

use App\Model\Db\Entity\User;
use Nextras\Orm\Repository\Repository;
use Override;

class UserRepository extends Repository
{
    public function getByUsername(string $username): User|null
    {
        $user = $this->getBy([
            'username' => $username,
        ]);
        assert($user instanceof User || !$user);
        return $user;
    }

    #[Override]
    public static function getEntityClassNames(): array
    {
        return [User::class];
    }
}