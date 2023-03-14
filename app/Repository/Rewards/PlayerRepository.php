<?php

namespace App\Repository\Rewards;

class PlayerRepository extends RewardsRepository
{
    public const TABLE_NAME = "player";
    public const COLUMN_ID = "id";
    public const COLUMN_NAME = "name";
    public const COLUMN_UUID = "uuid";
    public const COLUMN_NOT_DELETED = "not_deleted";

    protected string $tableName = self::TABLE_NAME;
}