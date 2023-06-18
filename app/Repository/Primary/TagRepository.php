<?php

namespace App\Repository\Primary;

class TagRepository extends PrimaryRepository
{
    public const TABLE_NAME = 'tag';
    public const COLUMN_ID = 'id';
    public const COLUMN_NAME = 'name';
    public const COLUMN_FONT_COLOR = 'font_color';
    public const COLUMN_BACKGROUND_COLOR = 'background_color';
    public const COLUMN_CREATED = 'created';
    public const COLUMN_CREATED_USER_ID = 'created_user_id';
    public const COLUMN_CHANGED = 'changed';
    public const COLUMN_CHANGED_USER_ID = 'changed_user_id';
    public const COLUMN_NOT_DELETED = 'not_deleted';

    protected string $tableName = self::TABLE_NAME;

    /**
     * Get items for select box
     * @return array
     */
    public function fetchItemsForChoiceControl(): array
    {
        return $this->findAll()
            ->fetchPairs(self::COLUMN_ID, self::COLUMN_NAME);
    }
}