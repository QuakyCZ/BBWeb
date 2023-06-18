<?php

namespace App\Modules\AdminModule\Component\Position;

use App\Component\BaseDataGrid;
use App\Enum\EFlashMessageType;
use App\Repository\Primary\PositionRepository;
use Nette\Application\BadRequestException;
use Nette\ComponentModel\IContainer;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\Localization\ITranslator;
use Tracy\Debugger;
use Tracy\ILogger;

class PositionGrid extends BaseDataGrid
{

    public function __construct(
        IContainer $parent,
        ITranslator $translator,
        private readonly PositionRepository $positionRepository,
    )
    {
        parent::__construct($parent, 'positions', $translator);
    }

    protected function getSelection(): Selection
    {
        return $this->positionRepository->findAll();
    }

    protected function createGrid(): void
    {
        $this->grid->addColumnText(PositionRepository::COLUMN_NAME, 'Název');
        $this->grid->addColumnText(PositionRepository::COLUMN_TEXT, 'Text');
        $this->grid->addColumnText(PositionRepository::COLUMN_URL, 'URL');
        $active = $this->grid->addColumnStatus(PositionRepository::COLUMN_ACTIVE, "Aktivní");
        $active->setEditableInputTypeSelect([
                0 => 'Ne',
                1 => 'Ano'
            ])
            ->setSortable()
            ->addOption(0, 'Ne')
            ->setClass('bg-danger')
            ->endOption()
            ->addOption(1, 'Ano')
            ->endOption()
            ->onChange[] = function($id, $newValue) {
                try {
                    $row = $this->positionRepository->findRow($id);
                    if ($row === null) {
                        throw new BadRequestException();
                    }
                    $row->update([
                        PositionRepository::COLUMN_ACTIVE => $newValue
                    ]);
                    $this->grid->presenter->flashMessage('Aktivita byla nastavena.', EFlashMessageType::SUCCESS);
                } catch (\PDOException $exception) {
                    Debugger::log($exception, ILogger::EXCEPTION);
                    $this->grid->presenter->flashMessage('Při zpracování požadavku nastala chyba.', EFlashMessageType::ERROR);
                }
            };
        $active->setFilterSelect([
            0 => 'Ne',
            1 => 'Ano',
            null => '-'
        ]);

        // PUBLISH
        $this->grid->addActionCallback('publish', '', function ($id) {
            try {
                $row = $this->positionRepository->findRow($id);
                if ($row === null) {
                    throw new BadRequestException();
                }
                $row->update([
                    PositionRepository::COLUMN_ACTIVE => 1
                ]);
                $this->grid->presenter->flashMessage('Pozice byla aktivována.', EFlashMessageType::SUCCESS);
            } catch (\PDOException $exception) {
                Debugger::log($exception, ILogger::EXCEPTION);
                $this->grid->presenter->flashMessage('Při zpracování požadavku nastala chyba.', EFlashMessageType::ERROR);
            }
        })
            ->setRenderCondition(function (ActiveRow $row) {
                return !$row[PositionRepository::COLUMN_ACTIVE];
            })
            ->setIcon('eye')
            ->setTitle('Aktivovat')
            ->setClass('btn btn-info');


        // UNPUBLISH
        $this->grid->addActionCallback('deactivate', '', function ($id) {
            try {
                $row = $this->positionRepository->findRow($id);
                if ($row === null) {
                    throw new BadRequestException();
                }
                $row->update([
                    PositionRepository::COLUMN_ACTIVE => 0
                ]);
                $this->grid->presenter->flashMessage('Publikování článeku bylo zrušeno.', EFlashMessageType::SUCCESS);
            } catch (\PDOException $exception) {
                Debugger::log($exception, ILogger::EXCEPTION);
                $this->grid->presenter->flashMessage('Při zpracování požadavku nastala chyba.', EFlashMessageType::ERROR);
            }
        })
            ->setIcon('eye-slash')
            ->setTitle('Deaktivovat')
            ->setRenderCondition(function (ActiveRow $row) {
                return ((bool) $row[PositionRepository::COLUMN_ACTIVE]);
            })
            ->setClass('btn btn-danger');

        // EDIT
        $this->grid->addAction('edit', '', ':Admin:Positions:edit', [
            'id' => 'id'
        ])
            ->setTitle('Upravit')
            ->setIcon('pen')
            ->setClass('btn btn-warning');

        // DELETE
        $this->grid->addActionCallback('delete', '', function ($id) {
            try {
                $this->positionRepository->setNotDeletedNull($id);
                $this->grid->presenter->flashMessage('Pozice byla smazána.', EFlashMessageType::SUCCESS);
            } catch (\PDOException $exception) {
                Debugger::log($exception, ILogger::EXCEPTION);
                $this->grid->presenter->flashMessage('Při zpracování požadavku nastala chyba.', EFlashMessageType::ERROR);
            }
        })
            ->setTitle($this->translator->translate('admin.articles.delete'))
            ->setIcon('trash')
            ->setClass('btn btn-danger');

        $this->grid->addToolbarButton(':Admin:Positions:add', 'Přidat pozici')
            ->setIcon('plus')
            ->setClass('btn btn-success');
    }
}
