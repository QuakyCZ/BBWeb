<?php

namespace App\Modules\AdminModule\Component\Server;

use App\Component\BaseComponent;
use App\Repository\Primary\ServerRepository;
use App\Repository\Primary\TagRepository;
use Nette\Application\BadRequestException;
use Nette\Forms\Form;
use Nette\Utils\ArrayHash;

class ServerForm extends BaseComponent
{

    public function __construct(
        private ?int $id,
        private ServerRepository $serverRepository,
        private TagRepository $tagRepository
    )
    {
    }

    public function render(): void
    {
        if ($this->id !== null)
        {
            $row = $this->serverRepository->findRow($this->id);
            if ($row === null)
            {
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
        $form->addText(ServerRepository::COLUMN_NAME, 'Název');
        $form->addMarkdown(ServerRepository::COLUMN_DESCRIPTION_SHORT, 'Krátký popis');
        $form->addMarkdown(ServerRepository::COLUMN_DESCRIPTION_FULL, 'Celý popis');
        $form->addMultiSelect("tag_ids", "Tagy", $this->tagRepository->fetchItemsForChoiceControl());

        $form->addSubmit('submit', 'Uložit');
        $form->onSuccess[] = [$this, 'saveForm'];
        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @return void
     */
    public function saveForm(Form $form, ArrayHash $values): void
    {
        $values[ServerRepository::COLUMN_ID] = $this->id;
        $this->serverRepository->save((array)$values);
    }
}

interface IServerFormFactory
{
    /**
     * @param int|null $id
     * @return ServerForm
     */
    public function create(?int $id = null): ServerForm;
}