<?php

namespace App\Repository\Primary;

use Nette\Database\Table\ActiveRow;
use Tomaj\NetteApi\Misc\TokenRepositoryInterface;

class UserTokenRepository extends PrimaryRepository implements TokenRepositoryInterface
{

    public const TABLE_NAME = 'user_token';
    public const COLUMN_ID = 'id';
    public const COLUMN_USER_ID = 'user_id';
    public const COLUMN_TOKEN = 'token';
    public const COLUMN_CREATED = 'created';
    public const COLUMN_NOT_DELETED = 'not_deleted';
    public const COLUMN_ALLOWED_IPS = 'allowed_ips';

    protected string $tableName = self::TABLE_NAME;

    public function getValidToken(string $token): ?ActiveRow
    {
        return $this->findBy([
            self::COLUMN_TOKEN => $token
        ])->fetch();
    }

    public function validToken(string $token): bool
    {
        return $this->getValidToken($token) !== null;
    }

    public function ipRestrictions(string $token): ?string
    {
        $row = $this->getValidToken($token);
        if ($row === null)
        {
            return null;
        }

        return $row[self::COLUMN_ALLOWED_IPS];
    }
}