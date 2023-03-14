<?php

namespace App\Modules\AdminModule\Component\Rewards;

class RewardsFormValues
{
    public string $name;
    public int $cooldown;
    /**
     * @var int[]
     */
    public array $server_ids;
    public array $permissions;
    public array $commands;
}