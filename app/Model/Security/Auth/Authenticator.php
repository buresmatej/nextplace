<?php

namespace App\Model\Security\Auth;

use App\Model\Db\Entity\User;
use App\Model\Db\Repository\UserRepository;
use Nette\Security\AuthenticationException;
use Nette\Security\Authenticator as NetteAuthenticator;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;
use Nette\Security\SimpleIdentity;
use Nette\Security\User as NsUser;

class Authenticator implements NetteAuthenticator
{
    public function __construct(
        private UserRepository $userRepository,
        private Passwords $passwords,
    ) {
    }

    function authenticate(string $username, string $password): IIdentity
    {
        $user = $this->userRepository->getByUsername($username);
        bdump($user);
        if (!$user) {
            throw new AuthenticationException('Invalid username or password.');
        }
        bdump('dreuha');
        if (!$this->passwords->verify($password, $user->password)) {
            throw new AuthenticationException('Invalid username or password.');
        }

        return new SimpleIdentity(
            $user->id,
            ['user']
        );
    }

    public function loginUser(
        NsUser $user,
        string $username,
        string $password
    ) {
        $user->login($username, $password);
    }
}