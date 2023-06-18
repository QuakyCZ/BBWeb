<?php

namespace App\Modules\AdminModule\Component\Server;

use App\Component\BaseComponent;
use App\Enum\EFlashMessageType;
use App\Repository\Primary\ServerRepository;
use App\Repository\Primary\ServerTagRepository;
use App\Repository\Primary\TagRepository;
use App\Utils\FileSystem\FileSystemException;
use App\Utils\FileSystem\LocalFileSystem;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Forms\Form;
use Nette\Http\FileUpload;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use Tracy\Debugger;

class ServerForm extends BaseComponent
{

    public function __construct(
        private ?int $id,
        private ServerRepository $serverRepository,
        private ServerTagRepository $serverTagRepository,
        private TagRepository $tagRepository,
        private LocalFileSystem $localFileSystem,
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

        $form->addUpload(ServerRepository::COLUMN_BANNER, 'Banner')
            ->addRule(Form::IMAGE, 'Banner musí být obrázek.')
            ->addRule(Form::MAX_FILE_SIZE, 'Maximální velikost obrázku je 20 MB.', 20000000);

        $form->addUpload(ServerRepository::COLUMN_CHARACTER, 'Postavička')
            ->addRule(Form::IMAGE, 'Postavička musí být obrázek.')
            ->addRule(Form::MAX_FILE_SIZE, 'Maximální velikost obrázku je 20 MB.', 20000000);

        $form->addCheckbox(ServerRepository::COLUMN_SHOW, "Zobrazit na webu.");

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

        /** @var FileUpload $banner */
        $banner = $values[ServerRepository::COLUMN_BANNER];

        /** @var FileUpload $character */
        $character = $values[ServerRepository::COLUMN_CHARACTER];

        $dir = '/files/images/servers/';

        try {

            $serverData = [
                ServerRepository::COLUMN_NAME => $values[ServerRepository::COLUMN_NAME],
                ServerRepository::COLUMN_DESCRIPTION_SHORT => $values[ServerRepository::COLUMN_DESCRIPTION_SHORT],
                ServerRepository::COLUMN_DESCRIPTION_FULL => $values[ServerRepository::COLUMN_DESCRIPTION_FULL],
                ServerRepository::COLUMN_SHOW => $values[ServerRepository::COLUMN_SHOW],
            ];

            if ($banner->hasFile()) {
                $bannerFileName = $this->localFileSystem->saveFileUpload($banner, $dir);
                $serverData[ServerRepository::COLUMN_BANNER] = $dir . $bannerFileName;
            }

            if ($character->hasFile()) {
                $characterFileName = $this->localFileSystem->saveFileUpload($character, $dir);
                $serverData[ServerRepository::COLUMN_CHARACTER] = $dir . $characterFileName;
            }

            if ($this->id !== null) {
                $serverData[ServerRepository::COLUMN_ID] = $this->id;
                $serverData[ServerRepository::COLUMN_CHANGED] = new DateTime();
                $serverData[ServerRepository::COLUMN_CHANGED_USER_ID] = $userId;
            } else {
                $serverData[ServerRepository::COLUMN_CRETED_USER_ID] = $userId;
            }

            $tagIds = $values['tag_ids'];

            $this->serverRepository->runInTransaction(function () use ($serverData, $tagIds, $userId) {
                $server = $this->serverRepository->save($serverData);

                if (!$server) {
                    throw new BadRequestException();
                }

                $this->serverTagRepository->saveTagsForServer(
                    $server[ServerRepository::COLUMN_ID],
                    $tagIds,
                    $userId,
                );
            });
            $this->presenter->flashMessage('Server byl úspěšně uložen.', EFlashMessageType::SUCCESS);
            $this->presenter->redirect('Servers:');
        } catch (\PDOException|FileSystemException $exception) {
            Debugger::log($exception);
            $form->addError('Chyba při ukládání.');
        }
    }
}
