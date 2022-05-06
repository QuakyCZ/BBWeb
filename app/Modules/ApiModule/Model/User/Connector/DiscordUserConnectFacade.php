<?php

namespace App\Modules\ApiModule\Model\User\Connector;

use App\Enum\EConnectTokenType;
use App\Modules\ApiModule\Model\User\UserConnectTokenFacade;
use App\Repository\Primary\UserDiscordAccountRepository;
use Tomaj\NetteApi\Response\JsonApiResponse;
use Tomaj\NetteApi\Response\ResponseInterface;
use Tracy\Debugger;
use Tracy\ILogger;

class DiscordUserConnectFacade extends BaseUserConnectFacade
{

    /**
     * @param UserConnectTokenFacade $userConnectTokenFacade
     * @param UserDiscordAccountRepository $userDiscordAccountRepository
     */
    public function __construct
    (
        UserConnectTokenFacade $userConnectTokenFacade,
        private UserDiscordAccountRepository $userDiscordAccountRepository,
    )
    {
        parent::__construct(EConnectTokenType::DISCORD, $userConnectTokenFacade);
    }

    /**
     * @param int $userId
     * @param array $data
     * @return ResponseInterface
     */
    public function connect(int $userId, array $data): ResponseInterface
    {
        $id = $data['id'];
        try
        {
            $this->userDiscordAccountRepository->save([
                UserDiscordAccountRepository::COLUMN_DISCORD_ID => $id,
                UserDiscordAccountRepository::COLUMN_USER_ID => $userId
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

    /**
     * @param int $userId
     * @return bool
     */
    public function disconnect(int $userId): bool
    {
        return $this->userDiscordAccountRepository->disconnect($userId) !== null;
    }
}