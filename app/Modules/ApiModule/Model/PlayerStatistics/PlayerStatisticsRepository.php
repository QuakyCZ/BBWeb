<?php

namespace App\Modules\ApiModule\Model\PlayerStatistics;

use App\Model\ContextLocator;
use App\Repository\BaseRepository;

class PlayerStatisticsRepository extends BaseRepository
{

    public const TABLE_NAME = 'player_statistics';

    protected string $tableName = self::TABLE_NAME;

    public function __construct(ContextLocator $contextLocator)
    {
        parent::__construct($contextLocator->getDungeonEscape());
    }
}