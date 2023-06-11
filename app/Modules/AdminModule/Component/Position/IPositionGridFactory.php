<?php

namespace App\Modules\AdminModule\Component\Position;

use Nette\ComponentModel\IContainer;

interface IPositionGridFactory
{
    /**
     * @param IContainer $parent
     * @return PositionGrid
     */
    public function create(IContainer $parent): PositionGrid;
}