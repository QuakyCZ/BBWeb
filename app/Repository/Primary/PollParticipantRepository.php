<?php

namespace App\Repository\Primary;

class PollParticipantRepository extends PrimaryRepository
{
    public const TABLE_NAME = 'poll_participant';
    protected string $tableName = self::TABLE_NAME;

    public const COLUMN_ID = 'id';
    public const COLUMN_POLL_ID = 'poll_id';
    public const COLUMN_POLL_OPTION_ID = 'poll_option_id';
    public const COLUMN_USER_ID = 'user_id';
    public const COLUMN_CREATED = 'created';
    public const COLUMN_CHANGED = 'changed';
    public const COLUMN_IS_EXTRA = 'is_extra';

    /**
     * @param int $pollId
     * @return int
     */
    public function deleteParticipants(int $pollId): int {
        return $this->findBy([
            self::COLUMN_POLL_ID => $pollId
        ])->delete();
    }
}