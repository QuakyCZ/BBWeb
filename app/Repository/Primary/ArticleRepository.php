<?php

namespace App\Repository\Primary;

class ArticleRepository extends PrimaryRepository
{
    public const TABLE_NAME = 'article';

    public const COLUMN_ID = 'id';
    public const COLUMN_TITLE = 'title';
    public const COLUMN_TEXT = 'text';
    public const COLUMN_CREATED = 'created';
    public const COLUMN_CREATED_USER_ID = 'created_user_id';
    public const COLUMN_CHANGED = 'changed';
    public const COLUMN_CHANGED_USER_ID = 'changed_user_id';
    public const COLUMN_IS_PUBLISHED = 'is_published';
    public const COLUMN_IS_PINNED = 'is_pinned';
    public const COLUMN_NOT_DELETED = 'not_deleted';

    protected string $tableName = self::TABLE_NAME;

    public function setPublished(int $articleId, bool $published = true)
    {

    }

}
