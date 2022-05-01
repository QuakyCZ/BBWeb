<?php

namespace App\Repository\DungeonEscape;

use App\Model\ContextLocator;
use App\Repository\BaseRepository;
use Nette\Database\Table\ActiveRow;

class PlayerRepository extends DungeonEscapeRepository
{

    public const TABLE_NAME = 'player';
    public const COLUMN_ID = 'id';
    public const COLUMN_NAME = 'name';
    public const COLUMN_UUID = 'uuid';
    public const COLUMN_STATISTICS_ID = 'statistics_id';
    public const COLUMN_CREATED = 'created';
    /**
     * @deprecated use PlayerRepository::COLUMN_NOT_DELETED
     */
    public const COLUMN_DELETED = 'deleted';
    public const COLUMN_NOT_DELETED = 'not_deleted';

    protected string $tableName = self::TABLE_NAME;

    public function getByUuid(string $uuid): ?ActiveRow
    {
        return $this->findBy([
            self::COLUMN_UUID => $uuid
        ], true)->fetch();
    }
}