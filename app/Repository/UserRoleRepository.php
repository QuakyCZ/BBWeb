<?php

namespace App\Repository;

use Nette\Database\Context;
use Nette\Database\Table\Selection;

class UserRoleRepository extends BaseRepository {

    const TABLE_NAME = "user_role";
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

        $string = '';
        foreach ($excludeRoleIds as $id)
            $string.=$id.',';

        return $this->database->table(self::TABLE_NAME)
            ->select('role.name, user_id, role_id, MIN(role_id), user.username')
            ->joinWhere(RoleRepository::TABLE_NAME,'role.id=user_role.role_id AND role.active=1 AND role.not_deleted=1')
            ->joinWhere(UserRepository::TABLE_NAME, 'user.id=user_role.user_id AND user.active=1 AND user.not_deleted=1')
            ->where('role_id NOT IN (?) AND user_role.active=1 AND user_role.not_deleted=1', $string)
            ->group('user_id');
    }
}