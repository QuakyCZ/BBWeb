<?php

namespace App\Repository\Primary;

use Nette\Database\Table\Selection;

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

    /**
     * Get selection of most recent articles
     * @param int $limit the limit of articles to get
     * @return Selection selection of articles
     */
    public function getMostRecentArticles(int $limit = 3): Selection
    {
        return $this->findAll()
            ->where(self::COLUMN_IS_PUBLISHED, true)
            ->limit($limit)
            ->order(self::COLUMN_IS_PINNED . ' DESC, ' . self::COLUMN_CREATED . ' DESC, ' . self::COLUMN_ID . ' DESC');
    }
}
