<?php

namespace App\Repository\Primary;

use Nette\Database\Table\ActiveRow;

class UserMinecraftAccountRepository extends PrimaryRepository
{
    public const TABLE_NAME = 'user_minecraft_account';
    public const COLUMN_ID = 'id';
    public const COLUMN_USER_ID = 'user_id';
    public const COLUMN_UUID = 'uuid';
    public const COLUMN_NICK = 'nick';
    public const COLUMN_NOT_DELETED = 'not_deleted';
    public const COLUMN_CREATED = 'created';

    protected string $tableName = self::TABLE_NAME;

    /**
     * @param int $userId
     * @return ActiveRow|null
     */
    public function getAccountByUserId(int $userId): ?ActiveRow
    {
        return $this->findBy([self::COLUMN_USER_ID => $userId])->fetch();
    }

    /**
     * @param string $uuid
     * @return ActiveRow|null
     */
    public function getAccountByUUID(string $uuid): ?ActiveRow
    {
        return $this->findBy([
            self::COLUMN_UUID => $uuid
        ])->fetch();
    }
}