<?php

namespace App\Modules\ApiModule\Model\User;

use App\Repository\Primary\UserConnectTokenRepository;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\Random;

class UserConnectTokenFacade
{
    public function __construct
    (
        private UserConnectTokenRepository $userConnectTokenRepository
    )
    {
    }

    /**
     * @return string
     */
    public function generateToken(): string
    {
        return implode('-', str_split(Random::generate(16,'0-9A-Z'),4));
    }

    /**
     * @param string $type
     * @param int $userId
     * @param string|null $token
     * @return ActiveRow
     */
    public function saveToken(string $type, int $userId, ?string $token = null): ActiveRow
    {

        $this->userConnectTokenRepository->deletePreviousTokens($userId, $type);

        if ($token === null)
        {
            $token = $this->generateToken();
        }

        return $this->userConnectTokenRepository->save([
            UserConnectTokenRepository::COLUMN_USER_ID => $userId,
            UserConnectTokenRepository::COLUMN_TOKEN => $token,
            UserConnectTokenRepository::COLUMN_TYPE => $type,
            UserConnectTokenRepository::COLUMN_VALID_TO => (new \DateTime())->add(new \DateInterval('PT30M'))
        ]);
    }

    /**
     * @param string $token
     * @param string $type
     * @return ActiveRow|null
     */
    public function getToken(string $token, string $type): ?ActiveRow
    {
        return $this->userConnectTokenRepository->getToken($token, $type);
    }

    /**
     * @param string $token
     * @param string $type
     * @return ActiveRow|null
     */
    public function validateToken(string $token, string $type): ?ActiveRow
    {
        $row = $this->getToken($token, $type);
        if ($row === null)
        {
            return null;
        }
        $row->update([
            'used' => true
        ]);
        return $row;
    }
}