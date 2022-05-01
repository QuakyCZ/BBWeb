<?php

namespace App\Modules\ApiModule\Model\PlayerStatistics;

use App\Repository\DungeonEscape\DungeonEscapeRepository;
use Nette\Database\Table\ActiveRow;

class PlayerStatisticsRepository extends DungeonEscapeRepository
{

    public const TABLE_NAME = 'player_statistics';
    public const COLUMN_ID = 'id';

    protected string $tableName = self::TABLE_NAME;

    public function getById(int $id): ?ActiveRow
    {
        return $this->findBy([
            self::COLUMN_ID => 1
        ])->fetch();
    }
}