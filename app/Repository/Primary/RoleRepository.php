<?php

namespace App\Repository\Primary;

class RoleRepository extends PrimaryRepository
{
    public const TABLE_NAME = 'role';

    public const COLUMN_ID = 'id';
    public const COLUMN_NAME = 'name';
    public const COLUMN_PRIORITY = 'priority';
    public const COLUMN_CREATED = 'created';
    public const COLUMN_NOT_DELETED = 'not_deleted';

    protected string $tableName = self::TABLE_NAME;

    public function getDataForSelect(): array
    {
        return $this->database->table(self::TABLE_NAME)
            ->where('not_deleted=1')
            ->fetchPairs('id', 'name');
    }
}
