<?php

namespace App\Repository\Rewards;

use App\Model\ContextLocator;
use App\Repository\BaseRepository;

abstract class RewardsRepository extends BaseRepository
{
    public function __construct(ContextLocator $contextLocator)
    {
        parent::__construct($contextLocator->getRewards());
    }
}