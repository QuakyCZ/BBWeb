<?php

namespace App\Repository\Rewards;

class RewardPlayerRepository extends RewardsRepository
{
    public const TABLE_NAME = "reward_player";

    public const COLUMN_ID = "id";
    public const COLUMN_REWARD_ID = "reward_id";
    public const COLUMN_PLAYER_ID = "player_id";

    public const COLUMN_TIMESTAMP = "timestamp";
    public const COLUMN_NOT_DELETED = "not_deleted";

    protected string $tableName = self::TABLE_NAME;
}