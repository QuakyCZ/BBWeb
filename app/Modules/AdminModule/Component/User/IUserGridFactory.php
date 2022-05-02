<?php

namespace App\Modules\AdminModule\Component\User;

use Nette\ComponentModel\IContainer;

interface IUserGridFactory
{
    /**
     * @param IContainer $parent
     * @return UserGrid
     */
    public function create(IContainer $parent): UserGrid;
}