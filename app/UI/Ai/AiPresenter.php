<?php

declare(strict_types=1);

namespace App\UI\Ai;

use App\Components\AiRecommendation\Datagrid\PresenterTrait as AiRecommendationDatagridPresenterTrait;
use Nette\Application\UI\Presenter;

class AiPresenter extends Presenter
{
    use AiRecommendationDatagridPresenterTrait;

    public function actionRecommendation(): void
    {
        if (!$this->getUser()->isAllowed('Recommendation', 'see')) {
            $this->redirect('Sign:login');
        }
    }
}