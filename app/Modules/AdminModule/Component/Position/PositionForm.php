<?php

namespace App\Modules\AdminModule\Component\Position;

use App\Enum\EFlashMessageType;
use App\Repository\Primary\PositionRepository;
use Nette\Application\BadRequestException;
use Nette\Database\UniqueConstraintViolationException;
use Nette\Forms\Form;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use Tracy\Debugger;
use Tracy\ILogger;

class PositionForm extends \App\Component\BaseComponent
{
    public function __construct(
        private ?int $id,
        private PositionRepository $positionRepository
    )
    {
    }

    public function render(): void
    {

        if ($this->id !== null) {
            $row = $this->positionRepository->findRow($this->id);
            if ($row === null) {
                throw new BadRequestException();
            }

            /** @var Form $form */
            $form = $this['form'];
            $form->setDefaults($row->toArray());
        }

        parent::render();
    }

    public function createComponentForm(): Form
    {
        $form = new \App\Form\Form();
        $form->addText(PositionRepository::COLUMN_NAME, 'Název')
            ->setRequired();
        $form->addText(PositionRepository::COLUMN_TEXT, 'Text');

        $form->addText(PositionRepository::COLUMN_URL, 'URL')
            ->setRequired();

        $form->addCheckbox(PositionRepository::COLUMN_ACTIVE, 'Aktivní');

        $form->addSubmit('submit', 'Uložit');

        $form->onSuccess[] = [$this, 'saveForm'];
        return $form;
    }

    public function saveForm(Form $form, ArrayHash $values): void {
        $userId = $this->presenter->user->id;
        unset($values['submit']);
        try {
            if ($this->id !== null) {
                $values[PositionRepository::COLUMN_ID] = $this->id;
                $values[PositionRepository::COLUMN_CHANGED] = new DateTime();
                $values[PositionRepository::COLUMN_CHANGED_USER_ID] = $userId;
            } else {
                $values[PositionRepository::COLUMN_CREATED_USER_ID] = $userId;
            }
            $this->positionRepository->save((array)$values);
            $this->presenter->flashMessage('Pozice byla uložena.', EFlashMessageType::SUCCESS);
            $this->presenter->redirect('Positions:');
        } catch (UniqueConstraintViolationException) {
            $form->addError('Pozice s tímto názvem již existuje.');
        } catch (\PDOException $exception) {
            Debugger::log($exception, ILogger::EXCEPTION);
            $form->addError('Při ukládání nastala chyba.');
        }
    }
}