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
    public function __construct
    (
        protected string $type,
        protected UserConnectTokenFacade $userConnectTokenFacade,
    )
    {
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
    abstract public function disconnect(int $userId): bool;
}