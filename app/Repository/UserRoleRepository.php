<?php

namespace App\Repository;

use Nette\Database\Context;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;

class UserRoleRepository extends BaseRepository {

    const TABLE_NAME = "user_role";

    public const COLUMN_ID = 'id';
    public const COLUMN_USER_ID = 'user_id';
    public const COLUMN_ROLE_ID = 'role_id';
    public const COLUMN_NOT_DELETED = 'not_deleted';

    protected string $tableName = self::TABLE_NAME;

    /**
     * @param int $userId
     * @return Selection
     */
    public function getUsersRoles(int $userId): Selection {
        return $this->database->table(self::TABLE_NAME)
            ->where('user_id', $userId);
    }

    /**
     * @param int $userId
     * @return array
     */
    public function getUsersRoleNames(int $userId): array {
        $roles = $this->getUsersRoles($userId)->fetchAll();
        $result = [];
        foreach ($roles as $role) {
            $result[] = $role->role['name'];
        }
        return $result;
    }

    public function getAllActive() {
        return $this->database->table(self::TABLE_NAME)
            ->select('*')
            ->joinWhere('role','role.id=user_role.role_id AND role.active=1 AND role.not_deleted=1')
            ->group('role.name')
            ->fetchAll();
    }

    public function getForAboutTeamListing(array $excludeRoleIds = []) {
        return $this->database->table(self::TABLE_NAME)
            ->where('role_id NOT IN (?)', $excludeRoleIds)
            ->where('user_role.not_deleted=1')
            ->where('role.not_deleted=1')
            ->where('user.not_deleted=1')
            ->where('user.active=1')
            ->group('user_id')->fetchAll();
    }

    /**
     * @param int $roleId
     * @return ActiveRow[]
     */
    public function getUsersByRole(int $roleId): array {
        /** @var ActiveRow[] $users */
        $users = [];
        $userRoles = $this->findBy([self::COLUMN_ROLE_ID => $roleId]);
        foreach ($userRoles as $userRole) {
            $users[] = $userRole->user->ref('user_id');
        }
        return $users;
    }
}