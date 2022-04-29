<?php

namespace App\Repository;

class ServerRepository extends BaseRepository {
    public const TABLE_NAME = "server";
    public const COLUMN_ID = 'id';
    public const COLUMN_NAME = 'name';
    public const COLUMN_API_URL = 'api_url';
    public const COLUMN_CRETED_USER_ID = 'created_user_id';
    public const COLUMN_CREATED = 'created';

    protected string $tableName = self::TABLE_NAME;

    /**
     * @return array
     */
    public function fetchItemsForChoiceControl(): array
    {
        return $this->findAll()->fetchPairs('id', 'name');
    }
}