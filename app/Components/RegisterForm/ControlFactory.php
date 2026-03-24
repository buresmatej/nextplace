<?php

declare(strict_types=1);

namespace App\Components\RegisterForm;

interface ControlFactory
{
    public function create(): Control;
}