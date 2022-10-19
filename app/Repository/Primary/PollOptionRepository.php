<?php

namespace App\Repository\Primary;

class PollOptionRepository extends PrimaryRepository
{
    public const TABLE_NAME = 'poll_option';
    protected string $tableName = self::TABLE_NAME;

    public const COLUMN_ID = 'id';
    public const COLUMN_POLL_ID = 'poll_id';
    public const COLUMN_TEXT = 'text';
    public const COLUMN_CREATED = 'created';
    public const COLUMN_CREATED_USER_ID = 'created_user_id';
    public const COLUMN_CHANGED = 'changed';
    public const COLUMN_CHANGED_USER_ID = 'changed_user_id';
    public const COLUMN_NOT_DELETED = 'not_deleted';

    /**
     * @param int $pollId
     * @return int
     */
    public function deleteOptionsForPoll(int $pollId): int {
        return $this->findBy([self::COLUMN_POLL_ID => $pollId])->delete();
    }
}