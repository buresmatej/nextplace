<?php

declare(strict_types=1);

namespace App\UI\DestinationLog;

use App\Components\DestinationLogForm\PresenterTrait as DestinationLogFormPresenterTrait;
use Nette\Application\UI\Presenter;

class DestinationLogPresenter extends Presenter
{
    use DestinationLogFormPresenterTrait;
}