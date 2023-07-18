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
use Nette\Utils\ArrayHash;
use Nette\Utils\Html;
use Tracy\Debugger;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;

class ServerGrid extends BaseDataGrid
{

    public function __construct(
        IContainer $parent,
        ITranslator $translator,
        private ServerRepository $serverRepository,
        private TagRepository $tagRepository,
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

        $this->grid->addColumnImage(ServerRepository::COLUMN_BANNER, "Banner");
        $this->grid->addColumnImage(ServerRepository::COLUMN_CHARACTER, "Postavička");

        $this->grid->addColumnStatus(ServerRepository::COLUMN_SHOW, "Zobrazit na webu")
            ->setSortable()
            ->addOption(0, 'Ne')
            ->setClass('bg-danger')
            ->endOption()
            ->addOption(1, 'Ano')
            ->endOption()
            ->setFilterSelect([
                0 => 'Ne',
                1 => 'Ano',
                null => '-'
            ]);

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
            ->setTemplateEscaping(false)
            ->setFilterMultiSelect($this->tagRepository->fetchItemsForChoiceControl())
            ->setAttribute('class', 'form-control input-sm selectpicker form-control-sm multiselect2')
            ->setCondition(function (Selection $selection, ArrayHash $value) {
                $selection->where(
                    ':' . ServerTagRepository::TABLE_NAME . '.' . ServerTagRepository::COLUMN_TAG_ID . ' IN (?)',
                    (array)$value,
                );
            });

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
