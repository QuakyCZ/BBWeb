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
     * @param string $uuidBin Must be converted to binary. Eg. hex2bin(uuid)
     * @return ActiveRow|null
     */
    public function getAccountByUUID(string $uuidBin): ?ActiveRow
    {
        return $this->findBy([
            self::COLUMN_UUID => $uuidBin
        ])->fetch();
    }

    /**
     * @param int $userId
     * @param string $uuidBin
     * @param string $nick
     * @return ?ActiveRow
     */
    public function saveAccount(int $userId, string $uuidBin, string $nick): ?ActiveRow
    {
        return $this->save([
            self::COLUMN_USER_ID => $userId,
            self::COLUMN_UUID => $uuidBin,
            self::COLUMN_NICK => $nick
        ]);
    }

    /**
     * @param int $userId
     * @return bool
     */
    public function deleteAccount(int $userId): bool
    {
        $row = $this->getAccountByUserId($userId);
        if ($row === null) {
            return false;
        }

        $row->update([
            self::COLUMN_NOT_DELETED => null
        ]);

        return true;
    }
}
