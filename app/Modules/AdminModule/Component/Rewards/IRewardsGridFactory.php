<?php

namespace App\Modules\AdminModule\Component\Rewards;

use Nette\ComponentModel\IContainer;

interface IRewardsGridFactory {
    /**
     * @param IContainer $parent
     * @return RewardsGrid
     */
    public function create(IContainer $parent): RewardsGrid;
}