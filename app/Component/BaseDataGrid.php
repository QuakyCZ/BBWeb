<?php

namespace App\Component;

use Nette\ComponentModel\IContainer;
use Nette\Database\Table\Selection;
use Nette\Localization\ITranslator;
use Nette\Localization\Translator;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

abstract class BaseDataGrid
{
    private IContainer $parent;

    protected DataGrid $grid;

    protected string $name;


    public function __construct
    (
        IContainer  $parent,
        string      $name,
        ITranslator $translator
    )
    {
        $this->parent = $parent;
        $this->translator = $translator;
        $this->name = $name;
    }

    abstract protected function getSelection(): Selection;

    abstract protected function createGrid(): void;

    /**
     * @return DataGrid
     */
    final public function create(): DataGrid
    {
        $this->grid = new DataGrid($this->parent, $this->name);
        $this->grid->setDataSource($this->getSelection());
        $this->grid->setTranslator($this->translator);
        $this->createGrid();
        return $this->grid;
    }
}