<?php

namespace App\Modules\AdminModule\Component\Tag;

use App\Enum\EFlashMessageType;
use App\Repository\Primary\TagRepository;
use Nette\ComponentModel\IContainer;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\Localization\ITranslator;
use Nette\Utils\Html;
use PDOException;
use Tracy\Debugger;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;

class TagGrid extends \App\Component\BaseDataGrid
{

    public function __construct(
        IContainer $parent,
        ITranslator $translator,
        private TagRepository $tagRepository,
    )
    {
        parent::__construct(
            $parent,
            "tags",
            $translator
        );
    }

    /**
     * @return Selection
     */
    protected function getSelection(): Selection
    {
        return $this->tagRepository->findAll();
    }

    /**
     * @return void
     */
    protected function createGrid(): void
    {
        $this->grid->addColumnText(TagRepository::COLUMN_NAME, 'Název')
            ->setRenderer(function (ActiveRow $row) {
                return Html::el('span')
                    ->class('badge')
                    ->style('background-color',htmlspecialchars($row[TagRepository::COLUMN_BACKGROUND_COLOR]))
                    ->style('color', htmlspecialchars($row[TagRepository::COLUMN_FONT_COLOR]))
                    ->setText(htmlspecialchars($row[TagRepository::COLUMN_NAME]));
            })->setTemplateEscaping(false)
            ->setSortable()
            ->setFilterText();

        $this->grid->addColumnText(TagRepository::COLUMN_FONT_COLOR, 'Barva textu');
        $this->grid->addColumnText(TagRepository::COLUMN_BACKGROUND_COLOR, 'Barva pozadí');
        $this->grid->addAction('edit', '', 'edit', ['id' => 'id'])
            ->setIcon('pencil-alt')
            ->setTitle('Upravit')
            ->setClass('btn btn-warning');
        $this->grid->addActionCallback(
            'delete',
            '',
            function (string $id) {
                try {
                    $this->tagRepository->setNotDeletedNull((int)$id);
                    $this->grid->presenter->flashMessage('Tag byl smazán.', EFlashMessageType::WARNING);
                } catch (PDOException $exception) {
                    Debugger::log($exception);
                    $this->grid->presenter->flashMessage('Při zpracování požadavku došlo k chybě.', EFlashMessageType::ERROR);
                }
                $this->grid->redrawControl();
                $this->grid->presenter->redrawControl();
            },
        )->setConfirmation(new StringConfirmation('Opravdu chcete smazat tag %s?', 'name'))
        ->setIcon('trash')
        ->setClass('btn btn-danger ajax');

        $this->grid->addToolbarButton('add', 'Přidat tag')
            ->setClass('btn btn-success')
            ->setIcon('plus');
    }
}