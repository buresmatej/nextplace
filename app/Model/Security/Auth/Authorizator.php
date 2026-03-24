<?php

namespace App\Model\Security\Auth;

use Nette\Security\Permission;

class Authorizator
{
    public static function create(): Permission
    {
        $acl = new Permission();
        $acl->addRole('guest');
        $acl->addRole('user', 'guest');
        $acl->addRole('admin', 'user');

        $acl->addResource('Sign');

        $acl->deny('guest');
        $acl->allow('guest', 'Sign', ['in', 'up']);


        $acl->deny('user', 'Sign', ['in', 'up']);
        $acl->allow('user', 'Sign', 'out');

        $acl->allow('admin');

        return $acl;
    }
}