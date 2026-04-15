<?php

declare(strict_types=1);

namespace App\Components\UserStats\Map;

interface ControlFactory
{
    public function create(): Control;
}