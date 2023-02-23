<?php

namespace App\Modules\ApiModule\Model\User\Connector;

use App\Modules\ApiModule\Model\User\UserConnectTokenFacade;
use App\Modules\ApiModule\Model\User\UserFacade;
use App\Repository\Primary\UserConnectTokenRepository;
use Nette\Application\BadRequestException;
use Nette\Database\Table\ActiveRow;
use Tomaj\NetteApi\Response\ResponseInterface;

abstract class BaseUserConnectFacade
{
    /**
     * @param string $type
     * @param UserConnectTokenFacade $userConnectTokenFacade
     */
    public function __construct(
        protected string $type,
        protected UserConnectTokenFacade $userConnectTokenFacade,
    ) {
    }

    /**
     * @param int $userId
     * @return ActiveRow
     */
    public function generateToken(int $userId): ActiveRow
    {
        $token = $this->userConnectTokenFacade->generateToken();
        return $this->userConnectTokenFacade->saveToken($this->type, $userId, $token);
    }

    /**
     * @param string $token
     * @param array|null $data
     * @return ActiveRow|null
     */
    public function validateToken(string $token, ?array $data = []): ?ActiveRow
    {
        return $this->userConnectTokenFacade->validateToken($token, $this->type);
    }

    /**
     * @param int $userId
     * @param array $data
     * @return ResponseInterface
     */
    abstract public function connect(int $userId, array $data): ResponseInterface;

    /**
     * @param int $userId
     * @return bool
     */
    public function isConnected(int $userId): bool
    {
        return $this->getAccount($userId) !== null;
    }

    /**
     * @param int $userId
     * @return ActiveRow|null
     */
    abstract public function getAccount(int $userId): ?ActiveRow;

    /**
     * @param int $userId
     * @return bool
     */
    abstract public function disconnect(int $userId): bool;
}
