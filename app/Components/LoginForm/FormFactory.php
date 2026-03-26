<?php

declare(strict_types=1);

namespace App\Components\LoginForm;

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
        private Authenticator $authenticator,
        private NsUser $user,
    ) {
    }

    public function create(): Form
    {
        $form = new Form();
        $form->addText('username', 'Username')
            ->setRequired('Required');
        $form->addPassword('password', 'Password')//TODO: pw verification, security checks
            ->setRequired('Required');

        $form->addSubmit('submit', 'Login');

        $form->onSuccess[] = $this->onSuccess(...);

        return $form;
    }

    public function onSuccess(Form $form): void
    {
        $values = $form->getValues('array');

        try {
            $this->authenticator->loginUser($this->user, $values['username'], $values['password']);
        } catch (AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }
}