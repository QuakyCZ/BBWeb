<?php

namespace App\Repository\Primary;

use App\Repository\BaseRepository;

class UserDetailsRepository extends BaseRepository {

    const TABLE_NAME = "user_details";
    protected string $tableName = self::TABLE_NAME;

    public function getDetails(int $userId, string $select = '*') {
        return $this->database->table(self::TABLE_NAME)
            ->select($select)
            ->where('user_id', $userId);
    }

}