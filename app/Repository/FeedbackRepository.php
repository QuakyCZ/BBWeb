<?php

namespace App\Repository;

class FeedbackRepository extends BaseRepository
{
    public const TABLE_NAME = 'feedback';

    public const COLUMN_NICK = 'nick';
    public const COLUMN_EMAIL = 'email';
    public const COLUMN_SERVER_ID = 'server_id';
    public const COLUMN_DESCRIPTION = 'description';
    public const COLUMN_NOT_DELETED = 'not_deleted';

    protected string $tableName = self::TABLE_NAME;
}