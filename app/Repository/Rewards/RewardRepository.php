<?php

namespace App\Repository\Rewards;

class RewardRepository extends RewardsRepository
{
    public const TABLE_NAME = 'reward';

    public const COLUMN_ID = 'id';
    public const COLUMN_NAME = 'name';
    public const COLUMN_COOLDOWN = 'cooldown';
    public const COLUMN_NOT_DELETED = 'not_deleted';

    protected string $tableName = self::TABLE_NAME;
}