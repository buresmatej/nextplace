<?php

declare(strict_types=1);

namespace App\Components\LoginForm;

use Closure;

interface ControlFactory
{
    public function create(Closure $onSuccess): Control;
}