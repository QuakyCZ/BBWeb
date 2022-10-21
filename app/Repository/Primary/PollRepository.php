<?php

namespace App\Repository\Primary;

use Nette\Database\Table\Selection;
use Nette\Utils\DateTime;

class PollRepository extends PrimaryRepository
{
    public const TABLE_NAME = 'poll';
    protected string $tableName = self::TABLE_NAME;

    public const COLUMN_ID = 'id';
    public const COLUMN_QUESTION = 'question';
    public const COLUMN_IS_PRIVATE = 'is_private';
    public const COLUMN_IS_ACTIVE = 'is_active';
    public const COLUMN_FROM = 'from';
    public const COLUMN_TO = 'to';
    public const COLUMN_ICON = 'icon';

    public const COLUMN_CREATED = 'created';
    public const COLUMN_CREATED_USER_ID = 'created_user_id';
    public const COLUMN_CHANGED = 'changed';
    public const COLUMN_CHANGED_USER_ID = 'changed_user_id';
    public const COLUMN_NOT_DELETED = 'not_deleted';

    public function isActive(int $pollId): bool {
        $poll = $this->getRow($pollId);

        if ($poll === null) {
            return false;
        }

        $now = new DateTime();

        return $poll[self::COLUMN_IS_ACTIVE] && $poll[self::COLUMN_FROM] <=$now && $poll[self::COLUMN_TO] >= $now;
    }

    public function getActivePolls(): Selection {
        $now = new DateTime();
        return $this->findBy([self::COLUMN_IS_ACTIVE => 1])
            ->where(self::COLUMN_FROM . ' <= ? AND ' . self::COLUMN_TO . ' > ?', $now, $now);
    }
}