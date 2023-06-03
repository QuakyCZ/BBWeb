<?php

namespace App\Modules\AdminModule\Component\Tag;

use App\Component\BaseComponent;
use App\Enum\EFlashMessageType;
use App\Form\Form;
use App\Repository\Primary\TagRepository;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use Tracy\Debugger;
use Tracy\ILogger;

class TagForm extends BaseComponent
{

    public function __construct(
        private ?int $id,
        private TagRepository $tagRepository
    )
    {
    }

    public function render(): void {
        if ($this->id !== null) {
            $row = $this->tagRepository->findRow($this->id);
            if ($row === null) {
                throw new BadRequestException();
            }

            /** @var Form $form */
            $form = $this['form'];
            $form->setDefaults($row->toArray());
        }
    }

    public function createComponentForm(): Form {
        $form = new Form();
        $form->addText(TagRepository::COLUMN_NAME, 'Název')
            ->setRequired();

        $form->addText(TagRepository::COLUMN_FONT_COLOR, 'Barva textu')
            ->setRequired();

        $form->addText(TagRepository::COLUMN_BACKGROUND_COLOR, 'Barva pozadí')
            ->setRequired();

        $form->addSubmit('submit', 'Uložit');
        $form->onSuccess[] = [$this, 'saveForm'];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function saveForm(Form $form, ArrayHash $values): void {
        $userId = $this->presenter->user->getId();
        try {
            $data = [
                TagRepository::COLUMN_NAME => $values[TagRepository::COLUMN_NAME],
                TagRepository::COLUMN_FONT_COLOR => $values[TagRepository::COLUMN_FONT_COLOR],
                TagRepository::COLUMN_BACKGROUND_COLOR => $values[TagRepository::COLUMN_BACKGROUND_COLOR],
            ];
            if ($this->id !== null) {
                $data[TagRepository::COLUMN_ID] = $this->id;
                $data[TagRepository::COLUMN_CHANGED_USER_ID] = $userId;
                $data[TagRepository::COLUMN_CHANGED] = new DateTime();
            } else {
                $data[TagRepository::COLUMN_CREATED_USER_ID] = $userId;
            }
            $this->tagRepository->save($data);
            $this->presenter->flashMessage('Tag byl uložen.', EFlashMessageType::SUCCESS);
            $this->presenter->redirect('Tags:');
        } catch (\PDOException $e) {
            Debugger::log($e, ILogger::EXCEPTION);
            $form->addError('Chyba při ukládání.');
        }
    }
}