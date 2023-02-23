<?php

namespace App\Repository\Primary;

use Nette\Database\Table\ActiveRow;

class UserDiscordAccountRepository extends PrimaryRepository
{
    public const TABLE_NAME = 'user_discord_account';
    public const COLUMN_ID = 'id';
    public const COLUMN_USER_ID = 'user_id';
    public const COLUMN_DISCORD_ID = 'discord_id';
    public const COLUMN_CREATED = 'created';
    public const COLUMN_NOT_DELETED = 'not_deleted';

    protected string $tableName = self::TABLE_NAME;

    /**
     * @param int $userId
     * @return ActiveRow|null
     */
    public function getAccountByUserId(int $userId): ?ActiveRow
    {
        return $this->findBy([
            self::COLUMN_USER_ID => $userId
        ])->fetch();
    }

    /**
     * @param int $id
     * @return int|null
     */
    public function markAsDeleted(int $id): ?int
    {
        return $this->findBy([
            self::COLUMN_ID => $id
        ])->fetch()?->update([
            self::COLUMN_NOT_DELETED => null
        ]);
    }

    /**
     * @param int $userId
     * @param int $discordId
     * @return ActiveRow|null
     */
    public function connect(int $userId, int $discordId): ?ActiveRow
    {
        return $this->save([
            self::COLUMN_USER_ID => $userId,
            self::COLUMN_DISCORD_ID => $discordId
        ]);
    }

    /**
     * @param int $userId
     * @return bool
     */
    public function disconnect(int $userId): bool
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
