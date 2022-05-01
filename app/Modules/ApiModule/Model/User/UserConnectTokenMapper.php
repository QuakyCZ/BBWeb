<?php

namespace App\Modules\ApiModule\Model\User;

use App\Repository\Primary\UserConnectTokenRepository;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

class UserConnectTokenMapper
{
    /**
     * @throws JsonException
     */
    public function mapToken(ActiveRow $row): UserConnectToken
    {
        return new UserConnectToken(
            $row[UserConnectTokenRepository::COLUMN_ID],
            $row[UserConnectTokenRepository::COLUMN_USER_ID],
            $row[UserConnectTokenRepository::COLUMN_TYPE],
            $row[UserConnectTokenRepository::COLUMN_TOKEN],
                Json::decode($row[UserConnectTokenRepository::COLUMN_DATA], Json::FORCE_ARRAY),
            $row[UserConnectTokenRepository::COLUMN_USED],
            $row[UserConnectTokenRepository::COLUMN_VALID_TO]
        );
    }
}