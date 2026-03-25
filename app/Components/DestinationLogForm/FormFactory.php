<?php

declare(strict_types=1);

namespace App\Components\DestinationLogForm;


use App\Model\Db\Repository\CountryRepository;
use Nette\Application\UI\Form;

class FormFactory
{
    public function __construct(
        private CountryRepository $countryRepository,
    ) {
    }

    public function create(): Form
    {
        $form = new Form();
        $form->addSelect(
            'country',
            'Country',
            $this->countryRepository->findForSelect(),
        );

        //TODO: star rating

        return $form;
    }
}