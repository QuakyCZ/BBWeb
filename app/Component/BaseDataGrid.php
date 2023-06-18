<?php

namespace App\Component;

use Nette\ComponentModel\IContainer;
use Nette\Database\Table\Selection;
use Nette\Localization\ITranslator;
use Ublaboo\DataGrid\DataGrid;

abstract class BaseDataGrid
{
    protected CustomDataGrid $grid;


    public function __construct(
        protected IContainer $parent,
        protected string $name,
        protected ITranslator $translator
    ) {
    }

    abstract protected function getSelection(): Selection;

    abstract protected function createGrid(): void;

    /**
     * @return DataGrid
     */
    final public function create(): DataGrid
    {
        $this->grid = new CustomDataGrid(null, $this->name);
        $this->grid->setDataSource($this->getSelection());
        $this->grid->setTranslator($this->translator);
        $this->createGrid();
        return $this->grid;
    }
}
