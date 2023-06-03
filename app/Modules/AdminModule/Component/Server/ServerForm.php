<?php

namespace App\Modules\AdminModule\Component\Server;

use App\Component\BaseComponent;
use App\Enum\EFlashMessageType;
use App\Repository\Primary\ServerRepository;
use App\Repository\Primary\ServerTagRepository;
use App\Repository\Primary\TagRepository;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Forms\Form;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use Tracy\Debugger;

class ServerForm extends BaseComponent
{

    public function __construct(
        private ?int $id,
        private ServerRepository $serverRepository,
        private ServerTagRepository $serverTagRepository,
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
            $defaults = $row->toArray();

            $serverTags = $row->related(ServerTagRepository::TABLE_NAME);
            foreach ($serverTags as $serverTag)
            {
                $defaults['tag_ids'][] = $serverTag[ServerTagRepository::COLUMN_TAG_ID];
            }

            /** @var Form $form */
            $form = $this['form'];
            $form->setDefaults($defaults);
        }

        parent::render();
    }

    public function createComponentForm(): Form
    {
        $form = new \App\Form\Form();
        $form->addText(ServerRepository::COLUMN_NAME, 'Název');
        $form->addMarkdown(ServerRepository::COLUMN_DESCRIPTION_SHORT, 'Krátký popis');
        $form->addMarkdown(ServerRepository::COLUMN_DESCRIPTION_FULL, 'Celý popis');
        $form->addMultiSelect2("tag_ids", "Tagy", $this->tagRepository->fetchItemsForChoiceControl());

        $form->addSubmit('submit', 'Uložit');
        $form->onSuccess[] = [$this, 'saveForm'];
        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @return void
     * @throws AbortException
     */
    public function saveForm(Form $form, ArrayHash $values): void
    {
        $userId = $this->presenter->user->id;
        try {
            $this->serverRepository->runInTransaction(function () use ($values, $userId) {
                $data = [
                    ServerRepository::COLUMN_NAME => $values[ServerRepository::COLUMN_NAME],
                    ServerRepository::COLUMN_DESCRIPTION_SHORT => $values[ServerRepository::COLUMN_DESCRIPTION_SHORT],
                    ServerRepository::COLUMN_DESCRIPTION_FULL => $values[ServerRepository::COLUMN_DESCRIPTION_FULL],
                ];

                if ($this->id !== null) {
                    $server = $this->serverRepository->findRow($this->id);
                    if ($server === null) {
                        throw new BadRequestException();
                    }
                    $data[ServerRepository::COLUMN_CHANGED] = new DateTime();
                    $data[ServerRepository::COLUMN_CHANGED_USER_ID] = $userId;
                    $server->update($data);
                } else {
                    $data[ServerRepository::COLUMN_CRETED_USER_ID] = $userId;
                    $server = $this->serverRepository->save($data);
                }

                $this->serverTagRepository->saveTagsForServer(
                    $server[ServerRepository::COLUMN_ID],
                    $values['tag_ids'],
                    $userId,
                );
            });
            $this->presenter->flashMessage('Server byl úspěšně uložen.', EFlashMessageType::SUCCESS);
            $this->presenter->redirect('Servers:');
        } catch (\PDOException $exception) {
            Debugger::log($exception);
            $form->addError('Chyba při ukládání.');
        }
    }
}
