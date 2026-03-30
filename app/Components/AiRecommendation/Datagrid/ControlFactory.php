<?php

declare(strict_types=1);

namespace App\Components\AiRecommendation\Datagrid;

interface ControlFactory
{
    public function create(): Control;
}