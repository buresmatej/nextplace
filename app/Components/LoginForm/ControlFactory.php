<?php

declare(strict_types=1);

namespace App\Components\LoginForm;

interface ControlFactory
{
    public function create(): Control;
}