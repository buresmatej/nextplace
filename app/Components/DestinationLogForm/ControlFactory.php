<?php

declare(strict_types=1);

namespace App\Components\DestinationLogForm;

interface ControlFactory
{
    public function create(): Control;
}