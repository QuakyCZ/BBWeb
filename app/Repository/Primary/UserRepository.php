<?php

namespace App\Repository\Primary;

use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;

class UserRepository extends PrimaryRepository
{
    public const TABLE_NAME = "user";

    public const COLUMN_ID = 'id';
    public const COLUMN_USERNAME = 'username';
    public const COLUMN_EMAIL = 'email';
    public const COLUMN_PASSWORD = 'password';
    public const COLUMN_VERIFICATION_TOKEN = 'verification_token';
    public const COLUMN_ACTIVE = 'active';

    public const COLUMN_CREATED = 'created';
    public const COLUMN_CREATED_USER_ID = 'created_user_id';
    public const COLUMN_REMOVED_AT = 'removed_at';
    public const COLUMN_NOT_DELETED = 'not_deleted';


    protected string $tableName = self::TABLE_NAME;

    /**
     * @return Selection
     */
    public function getForListing(): Selection {
        return $this->findAll()
            ->select(implode(', ', [
                self::COLUMN_ID, self::COLUMN_USERNAME, self::COLUMN_EMAIL, self::COLUMN_CREATED
            ]));
    }

    /**
     * @param string $email
     * @return ActiveRow|null
     */
    public function findByEmail(string $email): ?ActiveRow {
        return $this->findBy([
            self::COLUMN_EMAIL => $email
        ])->fetch();
    }

    /**
     * @param string $username
     * @return ActiveRow|null
     */
    public function findByUsername(string $username): ?ActiveRow {
        return $this->findBy([
            self::COLUMN_USERNAME => $username,
            self::COLUMN_ACTIVE => 1
        ])->fetch();
    }

    /**
     * @param string $login
     * @return ActiveRow|null
     */
    public function findByUsernameOrEmail(string $login): ?ActiveRow {
        return $this->findAll()
            ->where('(username=? OR email=?) AND active=1', $login, $login)
            ->fetch();
    }

    /**
     * @param int $id
     * @param bool $value
     * @return int
     */
    public function setActive(int $id, bool $value): int {
        return $this->findBy(['id' => $id], true)->update(['active' => $value]);
    }

    /**
     * Vrátí aktualizovaný řádek s uživatelem nebo null pokud neodpovídá token
     * @param int $userId
     * @param string $token
     * @return ActiveRow|null
     */
    public function verifyUser(int $userId, string $token): ?ActiveRow
    {
        $user = $this->findBy([
            self::COLUMN_ID => $userId,
            self::COLUMN_VERIFICATION_TOKEN => $token,
        ])->fetch();

        if ($user === null || $user[self::COLUMN_ACTIVE] === true)
        {
            return null;
        }

        $user->update([
            self::COLUMN_ACTIVE => 1,
            self::COLUMN_VERIFICATION_TOKEN => null
        ]);

        return $user;
    }
}