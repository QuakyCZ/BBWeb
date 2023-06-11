<?php

namespace App\Repository\Primary;

use Nette\Database\Table\Selection;

class PositionRepository extends PrimaryRepository
{
    public const TABLE_NAME = 'position';
    public const COLUMN_ID = 'id';
    public const COLUMN_NAME = 'name';
    public const COLUMN_TEXT = 'text';
    public const COLUMN_URL = 'url';
    public const COLUMN_ACTIVE = 'active';
    public const COLUMN_CREATED = 'created';
    public const COLUMN_CREATED_USER_ID = 'created_user_id';
    public const COLUMN_CHANGED = 'changed';
    public const COLUMN_CHANGED_USER_ID = 'changed_user_id';
    public const COLUMN_NOT_DELETED = 'not_deleted';

    protected string $tableName = self::TABLE_NAME;

    /**
     * Returns active positions
     * @return Selection
     */
    public function getActive(): Selection
    {
        return $this->findBy([
            self::COLUMN_ACTIVE => true
        ]);
    }
}