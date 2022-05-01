<?php

namespace App\Modules\ApiModule\Model\PlayerStatistics;

use App\Repository\DungeonEscape\DungeonEscapeRepository;

class PlayerStatisticsRepository extends DungeonEscapeRepository
{

    public const TABLE_NAME = 'player_statistics';
    public const COLUMN_ID = 'id';

    protected string $tableName = self::TABLE_NAME;
}