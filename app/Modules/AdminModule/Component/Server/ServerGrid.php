<?php

namespace App\Modules\AdminModule\Component\Server;

use App\Component\BaseDataGrid;
use App\Enum\EFlashMessageType;
use App\Repository\Primary\ServerRepository;
use Nette\ComponentModel\IContainer;
use Nette\Database\Table\Selection;
use Nette\Localization\ITranslator;
use Tracy\Debugger;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;

class ServerGrid extends BaseDataGrid
{

    public function __construct(
        IContainer $parent,
        ITranslator $translator,
        private ServerRepository $serverRepository
    )
    {
        parent::__construct($parent, "servers", $translator);
    }

    protected function getSelection(): Selection
    {
        return $this->serverRepository->findAll();
    }

    protected function createGrid(): void
    {
        $this->grid->addColumnText(ServerRepository::COLUMN_NAME, "Název");
        $this->grid->setItemsDetail(function ($row) {
            return $row[ServerRepository::COLUMN_DESCRIPTION_SHORT] . '<hr>' . $row[ServerRepository::COLUMN_DESCRIPTION_FULL];
        })
            ->setIcon("align-left")
            ->setClass("btn btn-default btn-secondary ajax");

        $this->grid->addAction("edit", "", "Servers:edit", ["id" => "id"])
            ->setTitle($this->translator->translate('admin.articles.edit'))
            ->setIcon('pen')
            ->setClass('btn btn-warning');

        $this->grid->addActionCallback("delete", "", function ($id) {
            try {
                $this->serverRepository->setNotDeletedNull($id);
                $this->grid->presenter->flashMessage("Server byl smazán.", EFlashMessageType::SUCCESS);
            } catch (\PDOException $exception) {
                Debugger::log($exception, ILogger::EXCEPTION);
                $this->grid->presenter->flashMessage("Při provádění požadavku nastala chyba.", EFlashMessageType::ERROR);
            }
        })
            ->setTitle($this->translator->translate('admin.articles.delete'))
            ->setIcon('trash')
            ->setClass('btn btn-danger')
            ->setConfirmation(new StringConfirmation('Opravdu chcete smazat tento server?'));

        $this->grid->addToolbarButton("Servers:add", "Přidat")
            ->setIcon("plus")
            ->setClass('btn btn-success');
    }
}

interface IServerGridFactory
{
    /**
     * @param IContainer $parent
     * @return ServerGrid
     */
    public function create(IContainer $parent): ServerGrid;
}