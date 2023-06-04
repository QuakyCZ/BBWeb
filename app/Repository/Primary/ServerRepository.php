<?php

namespace App\Repository\Primary;

use App\Repository\BaseRepository;

class ServerRepository extends PrimaryRepository
{
    public const TABLE_NAME = "server";
    public const COLUMN_ID = 'id';
    public const COLUMN_NAME = 'name';
    public const COLUMN_DESCRIPTION_SHORT = 'description_short';
    public const COLUMN_DESCRIPTION_FULL = 'description_full';

    public const COLUMN_BANNER = 'banner';
    public const COLUMN_CHARACTER = 'character';
    public const COLUMN_API_URL = 'api_url';

    public const COLUMN_CREATED = 'created';
    public const COLUMN_CRETED_USER_ID = 'created_user_id';

    public const COLUMN_CHANGED = 'changed';
    public const COLUMN_CHANGED_USER_ID = 'changed_user_id';

    public const COLUMN_NOT_DELETED = 'not_deleted';

    protected string $tableName = self::TABLE_NAME;

    /**
     * @return array
     */
    public function fetchItemsForChoiceControl(): array
    {
        return $this->findAll()->fetchPairs('id', 'name');
    }
}
