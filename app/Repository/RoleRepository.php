<?php

namespace App\Repository;

use Nette\Database\Context;

class RoleRepository extends BaseRepository {
    const TABLE_NAME = 'role';
    protected string $tableName = self::TABLE_NAME;

    public function getDataForSelect(): array {
        return $this->database->table(self::TABLE_NAME)
            ->where('active=1 AND not_deleted=1')
            ->fetchPairs('id','name');
    }
}