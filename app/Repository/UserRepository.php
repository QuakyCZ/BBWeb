<?php

namespace App\Repository;

use Nette\Database\Context;
use Nette\Database\Table\Selection;

class UserRepository extends BaseRepository
{
    const TABLE_NAME = "user";
    protected string $tableName = self::TABLE_NAME;

    /**
     * @return Selection
     */
    public function getForListing(): Selection {
        return $this->database->table(self::TABLE_NAME)
            ->select('id, username, email, created')
            ->where('not_deleted=1');
    }

    public function findByEmail(string $email) {
        return $this->database->table(self::TABLE_NAME)
            ->where('email=? AND active=1 AND not_deleted=1', $email)
            ->fetch();
    }

    public function findByUsername(string $username) {
        return $this->database->table(self::TABLE_NAME)
            ->where('username=? AND active=1 AND not_deleted=1', $username)
            ->fetch();
    }

    public function findByUsernameOrEmail(string $login) {
        return $this->database->table(self::TABLE_NAME)
            ->where('(username=? OR email=?) AND active=1 AND not_deleted=1', $login, $login)
            ->fetch();
    }
}