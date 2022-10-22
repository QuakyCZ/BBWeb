<?php

namespace App\Repository\Primary;

use Nette\Database\Table\ActiveRow;

class PollRoleRepository extends PrimaryRepository
{
    public const TABLE_NAME = 'poll_role';
    protected string $tableName = self::TABLE_NAME;
    public const COLUMN_ID = 'id';
    public const COLUMN_POLL_ID = 'poll_id';
    public const COLUMN_ROLE_ID = 'role_id';
    public const COLUMN_NOT_DELETED = 'not_deleted';

    /**
     * @param int $pollId
     * @return ActiveRow[]
     */
    public function getAllowedRolesForPoll(int $pollId): array {
        return array_map(
            function (ActiveRow $row) {
                return $row->ref(RoleRepository::TABLE_NAME);
            },
            $this->findBy([
                self::COLUMN_POLL_ID => $pollId
            ])->fetchAll()
        );
    }

}