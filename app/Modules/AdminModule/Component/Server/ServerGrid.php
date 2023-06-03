<?php

namespace App\Modules\AdminModule\Component\Server;

use App\Component\BaseDataGrid;
use App\Enum\EFlashMessageType;
use App\Repository\Primary\ServerRepository;
use App\Repository\Primary\ServerTagRepository;
use App\Repository\Primary\TagRepository;
use Nette\ComponentModel\IContainer;
use Nette\Database\Table\Selection;
use Nette\Localization\ITranslator;
use Nette\Utils\Html;
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
        $this->grid->addColumnText(ServerRepository::COLUMN_NAME, "Název")
            ->setSortable()
            ->setFilterText();

        $this->grid->addColumnText(ServerRepository::COLUMN_DESCRIPTION_SHORT, "Krátký popis");
        $this->grid->addColumnText("tags", "Tagy")
            ->setRenderer(function ($row) {
                $tags = "";
                foreach ($row->related(ServerTagRepository::TABLE_NAME) as $serverTag) {
                    $tag = $serverTag->tag;
                    $tags .= Html::el('span')
                        ->class('badge mx-1')
                        ->style('color', htmlspecialchars($tag[TagRepository::COLUMN_FONT_COLOR]))
                        ->style('background-color', htmlspecialchars($tag[TagRepository::COLUMN_BACKGROUND_COLOR]))
                        ->setText(htmlspecialchars($tag[TagRepository::COLUMN_NAME]))
                        ->toHtml();
                }
                return $tags;
            })
            ->setTemplateEscaping(false);

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