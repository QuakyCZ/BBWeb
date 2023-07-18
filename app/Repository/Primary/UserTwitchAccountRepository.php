<?php

namespace App\Repository\Primary;

use Nette\Database\Table\ActiveRow;

class UserTwitchAccountRepository extends PrimaryRepository
{
    public const TABLE_NAME = 'user_twitch_account';

    public const COLUMN_ID = 'id';
    public const COLUMN_USER_ID = 'user_id';
    public const COLUMN_TWITCH_ID = 'twitch_id';
    public const COLUMN_ACCESS_TOKEN = 'access_token';
    public const COLUMN_REFRESH_TOKEN = 'refresh_token';

    public const COLUMN_CREATED = 'created';
    public const COLUMN_NOT_DELETED = 'not_deleted';

    protected string $tableName = self::TABLE_NAME;

    /**
     * @param int $userId
     * @param string $twitchId
     * @param string $accessToken
     * @param string $refreshToken
     * @return ActiveRow
     */
    public function saveAccount(
        int $userId,
        string $twitchId,
        string $accessToken,
        string $refreshToken,
    ): ActiveRow {
        return $this->save([
            self::COLUMN_USER_ID => $userId,
            self::COLUMN_TWITCH_ID => $twitchId,
            self::COLUMN_ACCESS_TOKEN => $accessToken,
            self::COLUMN_REFRESH_TOKEN => $refreshToken,
        ]);
    }

    /**
     * Get account by user id.
     * @param int $userId
     * @return ActiveRow|null Table row if exists or null.
     */
    public function getAccountByUserId(int $userId): ?ActiveRow {
        return $this->findBy([
            self::COLUMN_USER_ID => $userId
        ])->fetch();
    }

    /**
     * Deletes twitch account
     * @param int $userId
     * @return bool False if the row was not found, otherwise true.
     */
    public function deleteAccount(int $userId): bool {
        $row = $this->getAccountByUserId($userId);
        if ($row === null) {
            return false;
        }

        return $row->update([
            self::COLUMN_NOT_DELETED => null,
        ]);
    }

}