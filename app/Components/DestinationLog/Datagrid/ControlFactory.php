<?php

declare(strict_types=1);

namespace App\Components\DestinationLog\Datagrid;

interface ControlFactory
{
    public function create(): Control;
}