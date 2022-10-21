<?php

namespace App\Modules\AdminModule\Component\Poll;

use App\Component\BaseDataGrid;
use App\Enum\EFlashMessageType;
use App\Repository\Primary\PollRepository;
use App\Repository\Primary\UserRepository;
use Contributte\Translation\Translator;
use Nette\Application\BadRequestException;
use Nette\ComponentModel\IContainer;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\Localization\ITranslator;
use Tracy\Debugger;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;
use Ublaboo\DataGrid\DataGrid;

class PollGrid extends BaseDataGrid
{
    public function __construct(
        IContainer $parent,
        ITranslator $translator,
        private PollRepository $pollRepository
    )
    {
        parent::__construct($parent, 'polls', $translator);
    }


    /**
     * @return Selection
     */
    protected function getSelection(): Selection
    {
        return $this->pollRepository->findAll();
    }

    protected function createGrid(): void
    {
        $this->grid->setDefaultSort('created DESC');

        $this->grid->addColumnText(PollRepository::COLUMN_QUESTION, 'Otázka');
        $this->grid->addColumnStatus('is_active', 'Aktivní')
            ->setRenderer(function (ActiveRow $row) {
                if ($this->pollRepository->isActive($row[PollRepository::COLUMN_ID])) {
                    return '<i class="fa-regular fa-circle-check text-success"></i>';
                }

                return '<i class="fa-regular fa-circle-xmark text-danger"></i>';
            });


        $this->grid->addColumnDateTime(PollRepository::COLUMN_FROM, 'Aktivní od')
            ->setFormat('d. m. Y H:i')
            ->setSortable();

        $this->grid->addColumnDateTime(PollRepository::COLUMN_TO, 'Aktivní do')
            ->setFormat('d. m. Y H:i')
            ->setSortable();

        $this->grid->addColumnDateTime(PollRepository::COLUMN_CREATED, 'Datum vytvoření')
            ->setFormat('d. m. Y H:i')
            ->setSortable();

        $this->grid->addColumnText(PollRepository::COLUMN_CREATED_USER_ID, 'Vytvořil')
            ->setRenderer(function (ActiveRow $row) {
                return $row->ref(UserRepository::TABLE_NAME)[UserRepository::COLUMN_USERNAME];
            });

        // Vysledky
        $this->grid->addAction('result', '', ':Admin:Poll:result', ['id' => 'id'])
            ->setIcon('poll')
            ->setClass('btn btn-info btn-large')
            ->setTitle('Výsledky');

        // Aktivovat
        $this->grid->addActionCallback('activate', '', function (string $id) {
            $poll = $this->pollRepository->getRow($id);
            if ($poll === null) {
                $this->grid->presenter->flashMessage('Hlasování nebylo nalezeno.', EFlashMessageType::ERROR);
                return;
            }
            try {
                $poll->update([
                    PollRepository::COLUMN_IS_ACTIVE => true
                ]);
                $this->grid->presenter->flashMessage('Hlasování bylo aktivováno.', EFlashMessageType::SUCCESS);
            } catch (\PDOException $exception) {
                Debugger::log($exception);
                $this->grid->presenter->flashMessage('Při zpracování požadavku nastala neznámá chyba.', EFlashMessageType::ERROR);
            }
        })->setRenderCondition(function (ActiveRow $row) {
            return !$row[PollRepository::COLUMN_IS_ACTIVE] && $row[PollRepository::COLUMN_TO] < new \DateTime();
        })->setIcon('thumbs-up')
            ->setTitle('Aktivovat');


        // Deaktivovat
        $this->grid->addActionCallback('deactivate', '', function (string $id) {
            $poll = $this->pollRepository->getRow($id);
            if ($poll === null) {
                $this->grid->presenter->flashMessage('Hlasování nebylo nalezeno.', EFlashMessageType::ERROR);
                return;
            }
            try {
                $poll->update([
                    PollRepository::COLUMN_IS_ACTIVE => false
                ]);
                $this->grid->presenter->flashMessage('Hlasování bylo deaktivováno.', EFlashMessageType::WARNING);
            } catch (\PDOException $exception) {
                Debugger::log($exception);
                $this->grid->presenter->flashMessage('Při zpracování požadavku nastala neznámá chyba.', EFlashMessageType::ERROR);
            }

        })->setRenderCondition(function (ActiveRow $row) {
            return $this->pollRepository->isActive($row[PollRepository::COLUMN_ID]);
        })->setIcon('thumbs-down')
        ->setTitle('Deaktivovat');


        // Editovat
        $this->grid->addAction('edit', '', ':Admin:Poll:edit', ['id' => 'id'])
            ->setRenderCondition(function (ActiveRow $item) {
                return !$this->pollRepository->isActive($item[PollRepository::COLUMN_ID]);
            })->setIcon('pen')
            ->setClass('btn btn-warning btn-large')
            ->setTitle('Upravit');


        // Smazat
        $this->grid->addActionCallback('delete', '', function (string $id) {
            $row = $this->pollRepository->getRow((int)$id);
            if ($row === null) {
                $this->grid->presenter->flashMessage('Hlasování nebylo nalezeno.', EFlashMessageType::ERROR);
                return;
            }
            try {
                $row->update([
                    PollRepository::COLUMN_NOT_DELETED => null
                ]);
                $this->grid->presenter->flashMessage('Hlasování bylo smazáno.', EFlashMessageType::WARNING);
            } catch (\PDOException $exception) {
                $this->grid->presenter->flashMessage('Při zpracování požadavku nastala neznámá chyba.', EFlashMessageType::ERROR);
                Debugger::log($exception);
            }
        })->setConfirmation(new StringConfirmation('Opravdu chcete odstranit hlasování?'))
            ->setIcon('trash')
            ->setClass('btn btn-danger btn-large')
            ->setTitle('Odstranit');

        $this->grid->addToolbarButton(':Admin:Poll:add', 'Přidat')
            ->setIcon('plus')
            ->setClass('btn btn-success btn-large');
    }
}

interface IPollGridFactory
{
    /**
     * @param IContainer $parent
     * @return PollGrid
     */
    public function create(IContainer $parent): PollGrid;
}