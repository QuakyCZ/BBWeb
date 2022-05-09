<?php

namespace App\Modules\ApiModule\Model\User\Connector;

use App\Enum\EConnectTokenType;
use App\Modules\ApiModule\Model\User\UserConnectTokenFacade;
use App\Modules\ApiModule\Model\User\UserFacade;
use App\Repository\Primary\UserMinecraftAccountRepository;
use App\Utils\MinecraftUtils;
use Nette\Application\BadRequestException;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\JsonException;
use Throwable;
use Tomaj\NetteApi\Response\JsonApiResponse;
use Tomaj\NetteApi\Response\ResponseInterface;
use Tracy\Debugger;
use Tracy\ILogger;

class MinecraftUserConnectFacade extends BaseUserConnectFacade
{

    public function __construct
    (
        UserConnectTokenFacade $userConnectTokenFacade,
        private UserFacade $userFacade,
        private UserMinecraftAccountRepository $userMinecraftAccountRepository
    )
    {
        parent::__construct(EConnectTokenType::MINECRAFT, $userConnectTokenFacade);
    }


    /**
     * @param int $userId
     * @param array $data
     * @return ResponseInterface
     * @throws Throwable
     */
    public function connect(int $userId, array $data): ResponseInterface
    {
        $uuid = $data['uuid'] ?? null;
        $nick = $data['nick'] ?? null;

        if ($uuid === null || $nick === null)
        {
            return new JsonApiResponse(400, [
                'status' => 'error',
                'message' => 'Nebylo specifikováno uuid nebo nick.'
            ]);
        }

        if ($this->isConnected($userId))
        {
            return new JsonApiResponse(400, [
                'status' => 'error',
                'message' => 'Tento účet je již spárován.'
            ]);
        }

        $uuidBin = MinecraftUtils::uuid2bin($uuid);

        if ($uuidBin === false)
        {
            return new JsonApiResponse(400, [
                'status' => 'error',
                'message' => 'Neplatný formát UUID'
            ]);
        }

        try
        {
            $this->userMinecraftAccountRepository->saveAccount($userId, $uuidBin, $nick);
            return new JsonApiResponse(200, [
                'status' => 'ok',
                'user_id' => $userId
            ]);
        }
        catch (\Exception $exception)
        {
            Debugger::log($exception, ILogger::EXCEPTION);
            return new JsonApiResponse(500, [
                'status' => 'error',
                'message' => 'Při zpracování požadavku nastala neznámá chyba.'
            ]);
        }
    }

    /**
     * @param int $userId
     * @return bool
     */
    public function isConnected(int $userId): bool
    {
        return $this->userMinecraftAccountRepository->getAccountByUserId($userId) !== null;
    }

    /**
     * @param int $userId
     * @return ActiveRow|null
     */
    public function getAccount(int $userId): ?ActiveRow
    {
        return $this->userMinecraftAccountRepository->getAccountByUserId($userId);
    }

    /**
     * @param int $userId
     * @return bool
     */
    public function disconnect(int $userId): bool
    {
        return $this->userMinecraftAccountRepository->deleteAccount($userId);
    }
}