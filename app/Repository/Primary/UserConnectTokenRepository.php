<?php

namespace App\Repository\Primary;

use Nette\Database\Table\ActiveRow;
use Nette\Utils\DateTime;

class UserConnectTokenRepository extends PrimaryRepository
{
    public const TABLE_NAME = 'user_connect_token';

    public const COLUMN_ID = 'id';
    public const COLUMN_USER_ID = 'user_id';
    public const COLUMN_TYPE = 'type';
    public const COLUMN_TOKEN = 'token';
    /**
     * JSON
     */
    public const COLUMN_DATA = 'data';
    public const COLUMN_USED = 'used';
    public const COLUMN_VALID_TO = 'valid_to';
    public const COLUMN_CREATED = 'created';

    public const COLUMN_NOT_DELETED = 'not_deleted';


    protected string $tableName = self::TABLE_NAME;

    /**
     * @param string $token
     * @param string $type
     * @return ActiveRow|null
     */
    public function getToken(string $token, string $type): ?ActiveRow
    {
        return $this->findBy([
            self::COLUMN_TOKEN => $token,
            self::COLUMN_TYPE => $type
            ])
            ->where(self::COLUMN_VALID_TO . ' >= ?', new DateTime())
            ->fetch();
    }

    /**
     * @param int $tokenId
     * @return int
     */
    public function markAsUsed(int $tokenId): int
    {
        return $this->findBy([self::COLUMN_ID => $tokenId])
            ->update([
                self::COLUMN_USED => true
            ]);
    }

    /**
     * @param int $userId
     * @param string $type
     * @return int
     */
    public function deletePreviousTokens(int $userId, string $type): int
    {
        return $this->findBy([
            self::COLUMN_USER_ID => $userId,
            self::COLUMN_TYPE => $type,
            self::COLUMN_USED => 0
        ])->update([
            self::COLUMN_NOT_DELETED => null
        ]);
    }
}
