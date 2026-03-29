<?php

declare(strict_types=1);

namespace App\Components\DestinationLog\Form;


use App\Model\Db\Entity\DestinationLog;
use App\Model\Db\Repository\CountryRepository;
use App\Model\Db\Repository\DestinationLogRepository;
use Nette\Application\UI\Form;
use Nette\Security\User;

class FormFactory
{
    public function __construct(
        private CountryRepository $countryRepository,
        private DestinationLogRepository $destinationLogRepository,
        private User $user,
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

        $form->addInteger('rating')
            ->setHtmlAttribute('type', 'hidden')
            ->setRequired('Rating required');

        $form->addTextArea('note', 'Note')
            ->setMaxLength(500);

        $form->addSubmit('submit', 'log');

        $form->onSuccess[] = $this->onSuccess(...);

        return $form;
    }


    public function onSuccess(Form $form)
    {
        $values = $form->getValues('array');
        $destinationLog = new DestinationLog();
        $destinationLog->country = $values['country'];
        $destinationLog->rating = $values['rating'];
        $destinationLog->note = $values['note'];
        $destinationLog->user = $this->user->id;
        $this->destinationLogRepository->attach($destinationLog);
        $this->destinationLogRepository->persistAndFlush($destinationLog);
    }
}