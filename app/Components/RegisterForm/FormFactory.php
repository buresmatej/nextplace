<?php

declare(strict_types=1);

namespace App\Components\RegisterForm;

use App\Model\Db\Entity\User;
use App\Model\Db\Repository\UserRepository;
use App\Model\Security\Auth\Authenticator;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Security\Passwords;
use Nette\Security\User as NsUser;

class FormFactory
{
    public function __construct(
        private UserRepository $userRepository,
        private Passwords $passwords,
        private Authenticator $authenticator,
        private NsUser $user,
    ) {
    }

    public function create(): Form
    {
        $form = new Form();
        $form->addText('username', 'Username')
            ->setMaxLength(100)
            ->setRequired('Required');
        $form->addEmail('email', 'Email') //TODO: check unique username, email
            ->setRequired('Required');
        $form->addPassword('password', 'Password')//TODO: pw verification, security checks
            ->setRequired('Required');

        $form->addSubmit('submit', 'Register');

        $form->onSuccess[] = $this->onSuccess(...);

        return $form;
    }

    public function onSuccess(Form $form): void
    {
        $values = $form->getValues('array');
        $password = $values['password'];
        $user = new User();
        $user->username = $values['username'];
        $user->email = $values['email'];
        $user->password = $this->passwords->hash($password);
        $this->userRepository->attach($user);
        $this->userRepository->persistAndFlush($user);
        try {
            $this->authenticator->loginUser($this->user, $user->username, $password);
        } catch (AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }
}