<?php

namespace App\Modules\ApiModule\Model\User\Connector;

use App\Enum\EConnectTokenType;
use App\Modules\ApiModule\Model\User\UserConnectTokenFacade;
use App\Repository\LuckPerms\LuckPermsUserPermissionsRepository;
use App\Repository\Primary\UserRepository;
use App\Repository\Primary\UserTwitchAccountRepository;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Nette\Database\Table\ActiveRow;
use NewTwitchApi\NewTwitchApi;
use Throwable;
use Tomaj\NetteApi\Response\JsonApiResponse;
use Tomaj\NetteApi\Response\ResponseInterface;
use Tracy\Debugger;

class TwitchUserConnectFacade extends BaseUserConnectFacade
{


    public function __construct(
        private readonly string $twitchRedirectUri,
        UserConnectTokenFacade $userConnectTokenFacade,
        private readonly NewTwitchApi $twitchApi,
        private readonly UserTwitchAccountRepository $userTwitchAccountRepository,
        private readonly UserRepository $userRepository,
        private readonly MinecraftUserConnectFacade $minecraftUserConnectFacade,
    )
    {
        parent::__construct(EConnectTokenType::TWITCH, $userConnectTokenFacade);
    }

    public function connect(int $userId, array $data): ResponseInterface
    {
        if (empty($data['code'])) {
            return new JsonApiResponse(400, ['error' => 'Missing code']);
        }

        /** @var string $code */
        $code = $data['code'];

        $oauth = $this->twitchApi->getOauthApi();

        try {
            $token = $oauth->getUserAccessToken($code, $this->twitchRedirectUri);

            if ($token->getStatusCode() !== 200) {
                return new JsonApiResponse(400, ['error' => 'Could not receive user access token. ' . $token->getStatusCode()]);
            }

            $data = json_decode($token->getBody()->getContents(), false, 512, JSON_THROW_ON_ERROR);

            $accessToken = $data->access_token ?? null;

            if ($accessToken === null) {
                return new JsonApiResponse(400, ['error' => 'Could not receive user access token.']);
            }

            $refreshToken = $data->refresh_token ?? null;

            if ($refreshToken === null) {
                return new JsonApiResponse(400, ['error' => 'Could not receive user refresh token.']);
            }

            $twitchId = $this->fetchTwitchUserId($accessToken);

            if ($twitchId === null) {
                return new JsonApiResponse(400, ['error' => 'Could not receive user id.']);
            }

            $this->userTwitchAccountRepository->saveAccount(
                $userId,
                $twitchId,
                $accessToken,
                $refreshToken,
            );

        } catch (Throwable $e) {
            Debugger::log($e, 'twitch');
            return new JsonApiResponse(400, ['error' => $e->getMessage()]);
        }

        return new JsonApiResponse(200, ['success' => true]);
    }

    /**
     * @param int $userId
     * @return ActiveRow|null
     */
    public function getAccount(int $userId): ?ActiveRow
    {
        return $this->userTwitchAccountRepository->getAccountByUserId($userId);
    }

    public function disconnect(int $userId): bool
    {
        return $this->userTwitchAccountRepository->deleteAccount($userId);
    }

    /**
     * @param string $accessToken
     * @return string|null
     * @throws GuzzleException
     * @throws JsonException
     */
    private function fetchTwitchUserId(string $accessToken): ?string
    {
        $response = $this->twitchApi->getUsersApi()->getUserByAccessToken($accessToken);

        // Get and decode the actual content sent by Twitch.
        $responseContent = json_decode($response->getBody()->getContents(), false, 512, JSON_THROW_ON_ERROR);

        // Return the first (or only) user.
        return $responseContent?->data[0]?->id ?? null;
    }


    /**
     * @return void
     * @throws GuzzleException
     */
    public function refreshSubscriptions(): void
    {
        $accounts = $this->userTwitchAccountRepository->findAll();

        $broadcasters = $this->userRepository->getRequiredBroadcastersForSubscription()->fetchAll();

        foreach ($accounts as $account) {
            $this->refreshSubscription(
                $account,
                $broadcasters,
            );
        }
    }

    /**
     * @param ActiveRow $account
     * @param ActiveRow[] $broadcasters
     * @return bool
     * @throws GuzzleException
     */
    private function refreshSubscription(ActiveRow $account, array $broadcasters): bool
    {
        $hasSub = true;

        foreach ($broadcasters as $broadcaster)
        {
            $twitchRow = $broadcaster->related(UserTwitchAccountRepository::TABLE_NAME)->fetch();

            if ($twitchRow === null)
            {
                Debugger::log($broadcaster[UserRepository::COLUMN_USERNAME] . ' vyžaduje sub, ale nemá propojený twitch!', 'twitch');
                return false;
            }

            $hasSub = $this->verifySubscription(
                $account,
                $twitchRow[UserTwitchAccountRepository::COLUMN_TWITCH_ID],
            );

            if (!$hasSub) {
                break;
            }
        }

        $this->minecraftUserConnectFacade->setSubserverPermission(
            $account[UserTwitchAccountRepository::COLUMN_USER_ID],
            $hasSub,
        );

        return $hasSub;
    }


    /**
     * @param int $userId
     * @return bool
     * @throws GuzzleException
     */
    public function refreshSubscriptionForUser(int $userId): bool
    {

        $account = $this->userTwitchAccountRepository->getAccountByUserId($userId);

        if ($account === null)
        {
            return false;
        }

        $broadcasters = $this->userRepository->getRequiredBroadcastersForSubscription()->fetchAll();

        return $this->refreshSubscription($account, $broadcasters);
    }

    /**
     * @throws GuzzleException
     */
    public function verifySubscription(
        ActiveRow $userTwitchAccountRow,
        string $broadcasterId,
    ): bool
    {
        $subscriptionsApi = $this->twitchApi->getSubscriptionsApi();
        try {
            $response = $subscriptionsApi->checkUserSubscription(
                $userTwitchAccountRow[UserTwitchAccountRepository::COLUMN_ACCESS_TOKEN],
                $broadcasterId,
                $userTwitchAccountRow[UserTwitchAccountRepository::COLUMN_TWITCH_ID],
            );
        } catch (GuzzleException $exception) {
            if ($exception->getCode() === 404) {
                return false;
            }

            throw $exception;
        }

        if ($response->getStatusCode() === 404) {
            return false;
        }

        if ($response->getStatusCode() === 401) {
            if (!$this->twitchApi->getOauthApi()->refreshToken($userTwitchAccountRow)) {
                return false;
            }

            return $this->verifySubscription($userTwitchAccountRow, $broadcasterId);
        }

        if ($response->getStatusCode() !== 200) {
            Debugger::log($response->getStatusCode() . ': ' . $response->getReasonPhrase(), 'twitch');
            return false;
        }

        return true;
    }

    /**
     * @param ActiveRow $userTwitchAccountRow
     * @return bool
     * @throws GuzzleException
     * @throws JsonException
     */
    private function refreshTwitchToken(ActiveRow $userTwitchAccountRow): bool {
        $oauth = $this->twitchApi->getOauthApi();

        $response = $oauth->refreshToken(
            $userTwitchAccountRow[UserTwitchAccountRepository::COLUMN_REFRESH_TOKEN],
            'user:read:subscriptions',
        );

        // If refresh token did not succeeded, user changed his password or disallowed the app. Disconnect account from db.
        if ($response->getStatusCode() !== 200) {
            $this->disconnect($userTwitchAccountRow[UserTwitchAccountRepository::COLUMN_USER_ID]);
            return false;
        }

        $responseContent = json_decode($response->getBody()->getContents(), false, 512, JSON_THROW_ON_ERROR);

        $userTwitchAccountRow->update([
            UserTwitchAccountRepository::COLUMN_ACCESS_TOKEN => $responseContent->access_token,
            UserTwitchAccountRepository::COLUMN_REFRESH_TOKEN => $responseContent->refresh_token,
        ]);

        return true;
    }

    /**
     * @return string
     */
    public function getOauthUri(): string
    {
        return $this->twitchApi->getOauthApi()
            ->getAuthUrl($this->twitchRedirectUri, 'code', 'user:read:subscriptions');
    }
}