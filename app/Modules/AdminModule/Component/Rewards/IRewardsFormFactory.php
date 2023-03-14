<?php

namespace App\Modules\AdminModule\Component\Rewards;

interface IRewardsFormFactory
{
    /**
     * @param int|null $id
     * @return RewardsForm
     */
    public function create(?int $id = null): RewardsForm;
}