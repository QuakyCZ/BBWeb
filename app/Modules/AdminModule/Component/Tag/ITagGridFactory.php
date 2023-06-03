<?php

namespace App\Modules\AdminModule\Component\Tag;

use Nette\ComponentModel\IContainer;

interface ITagGridFactory
{
    /**
     * @param IContainer $parent
     * @return TagGrid
     */
    public function create(IContainer $parent): TagGrid;
}