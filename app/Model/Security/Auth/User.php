<?php

namespace App\Model\Security\Auth;

use App\Model\Db\Entity\User as DbUser;
use App\Model\Db\Repository\UserRepository;
use Nette\Security\Authorizator;
use Nette\Security\IAuthenticator;
use Nette\Security\User as NsUser;
use Nette\Security\UserStorage;

class User extends NsUser
{
    public function __construct(
        private UserRepository $userRepository,
        UserStorage $storage,
        ?IAuthenticator $authenticator = null,
        ?Authorizator $authorizator = null,
    ) {
        parent::__construct($storage, $authenticator, $authorizator);
    }

    public function getLoggedUser(): DbUser
    {
        return $this->userRepository
            ->getById($this->id);
    }
}