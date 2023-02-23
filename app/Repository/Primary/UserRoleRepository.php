<?php

namespace App\Repository\Primary;

use Nette\Database\ResultSet;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;

class UserRoleRepository extends PrimaryRepository
{
    public const TABLE_NAME = "user_role";

    public const COLUMN_ID = 'id';
    public const COLUMN_USER_ID = 'user_id';
    public const COLUMN_ROLE_ID = 'role_id';
    public const COLUMN_NOT_DELETED = 'not_deleted';

    protected string $tableName = self::TABLE_NAME;

    /**
     * @param int $userId
     * @return Selection
     */
    public function getUsersRoles(int $userId): Selection
    {
        return $this->database->table(self::TABLE_NAME)
            ->where('user_id', $userId);
    }

    /**
     * @param int $userId
     * @return array
     */
    public function getUsersRoleNames(int $userId): array
    {
        $roles = $this->getUsersRoles($userId)->fetchAll();
        $result = [];
        foreach ($roles as $role) {
            $result[] = $role->role['name'];
        }
        return $result;
    }

    public function getAllActive()
    {
        return $this->database->table(self::TABLE_NAME)
            ->select('*')
            ->joinWhere('role', 'role.id=user_role.role_id AND role.active=1 AND role.not_deleted=1')
            ->group('role.name')
            ->fetchAll();
    }

    /**
     * @param array $excludeRoleNames
     * @return ResultSet
     */
    public function getForAboutTeamListing(array $excludeRoleNames = []): ResultSet
    {
        return $this->database->query("
            SELECT r.id, r.name, u.username, mc.nick, ud.position  FROM `user_role` ur
            LEFT JOIN role r ON ur.role_id = r.id
            JOIN user u ON u.id = ur.user_id
            LEFT JOIN user_details ud ON ur.user_id = ud.user_id AND ud.not_deleted=1
            LEFT JOIN user_minecraft_account mc ON mc.user_id = ur.user_id AND mc.not_deleted=1 
            WHERE u.not_deleted=1 AND r.name NOT IN (?)
            GROUP BY ur.user_id
            ORDER BY r.priority, ud.id;
        ", $excludeRoleNames);
    }

    /**
     * @param int $roleId
     * @return ActiveRow[]
     */
    public function getUsersByRole(int $roleId): array
    {
        $rows = $this->findAll()
            ->where(self::COLUMN_ROLE_ID, $roleId)
            ->where('user.not_deleted = 1')
            ->where('')
            ->where('role.not_deleted = 1');

        $result = [];

        foreach ($rows as $row) {
            $result[] = $row->ref(self::COLUMN_USER_ID);
        }

        return $result;
    }

    /**
     * @param int $userId
     * @return int
     */
    public function dropUserRoles(int $userId): int
    {
        return $this->findBy([
            self::COLUMN_USER_ID => $userId
        ])->delete();
    }
}
