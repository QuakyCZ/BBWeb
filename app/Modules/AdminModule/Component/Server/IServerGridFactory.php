<?php

namespace App\Modules\AdminModule\Component\Server;

use Nette\ComponentModel\IContainer;

interface IServerGridFactory
{
    /**
     * @param IContainer $parent
     * @return ServerGrid
     */
    public function create(IContainer $parent): ServerGrid;
}