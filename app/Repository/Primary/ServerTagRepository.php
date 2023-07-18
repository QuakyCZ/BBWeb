<?php

namespace App\Repository\Primary;

class ServerTagRepository extends PrimaryRepository
{
    public const TABLE_NAME = 'server_tag';
    public const COLUMN_ID = 'id';
    public const COLUMN_SERVER_ID = 'server_id';
    public const COLUMN_TAG_ID = 'tag_id';
    public const COLUMN_CREATED = 'created';
    public const COLUMN_CREATED_USER_ID = 'created_user_id';
    public const COLUMN_CHANGED = 'changed';
    public const COLUMN_CHANGED_USER_ID = 'changed_user_id';
    public const COLUMN_NOT_DELETED = 'not_deleted';

    protected string $tableName = self::TABLE_NAME;

    public function saveTagsForServer(int $serverId, array $tagIds, int $userId): void
    {

        $this->findBy([
            self::COLUMN_SERVER_ID => $serverId,
        ])->delete();

        foreach ($tagIds as $tagId) {
            $this->save([
                self::COLUMN_SERVER_ID => $serverId,
                self::COLUMN_TAG_ID => $tagId,
                self::COLUMN_CREATED_USER_ID => $userId,
            ]);
        }
    }
}