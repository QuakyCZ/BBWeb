<?php

namespace App\Repository\Rewards;

class ServerRepository extends RewardsRepository
{
    public const TABLE_NAME = "server";

    public const COLUMN_ID = "id";
    public const COLUMN_NAME = "name";
    public const COLUMN_NOT_DELETED = "not_deleted";

    protected string $tableName = self::TABLE_NAME;


    /**
     * Fetch servers for choice control
     * @return array<int, string>
     */
    public function fetchForChoiceControl(): array {
        return $this->findAll()->fetchPairs(self::COLUMN_ID, self::COLUMN_NAME);
    }
}