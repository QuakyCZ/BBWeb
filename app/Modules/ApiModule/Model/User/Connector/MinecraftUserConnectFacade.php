<?php

namespace App\Modules\ApiModule\Model\User\Connector;

use App\Enum\EConnectTokenType;
use App\Modules\ApiModule\Model\User\UserConnectTokenFacade;
use App\Modules\ApiModule\Model\User\UserFacade;
use App\Repository\Primary\UserMinecraftAccountRepository;
use Nette\Application\BadRequestException;
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
     * @throws BadRequestException
     * @throws JsonException
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

        $uuid = str_replace('-', '', $uuid);

        if (!ctype_xdigit($uuid) || strlen($uuid) % 2 !== 0)
        {
            return new JsonApiResponse(400, [
                'status' => 'error',
                'message' => 'Neplatný formát UUID'
            ]);
        }

        if ($this->userMinecraftAccountRepository->getAccountByUUID($uuid) !== null)
        {
            return new JsonApiResponse(200, [
                'status' => 'error',
                'message' => 'Tento účet je již spárován.'
            ]);
        }

        try
        {
            $this->userMinecraftAccountRepository->save([
                UserMinecraftAccountRepository::COLUMN_USER_ID => $userId,
                UserMinecraftAccountRepository::COLUMN_NICK => $nick,
                UserMinecraftAccountRepository::COLUMN_UUID => $uuid
            ]);
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

    public function disconnect(int $userId): bool
    {
        // TODO: Implement disconnect() method.
    }
}