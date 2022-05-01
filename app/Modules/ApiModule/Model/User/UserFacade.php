<?php

namespace App\Modules\ApiModule\Model\User;

use App\Enum\EConnectTokenType;
use App\Repository\Primary\UserConnectTokenRepository;
use App\Repository\Primary\UserDetailsRepository;
use App\Repository\Primary\UserMinecraftAccountRepository;
use App\Repository\Primary\UserRepository;
use DateInterval;
use Keygen\Keygen;
use Nette\Application\BadRequestException;
use Nette\NotImplementedException;
use Nette\Security\Passwords;
use Nette\Utils\DateTime;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Throwable;

class UserFacade
{
    public function __construct
    (
        private UserRepository $userRepository,
        private UserMinecraftAccountRepository $minecraftAccountRepository,
        private UserConnectTokenRepository $connectTokenRepository,
        private UserConnectTokenMapper $userConnectTokenMapper,
    )
    {
    }

    /**
     * @throws BadRequestException|JsonException|Throwable
     */
    public function connectMinecraft(int $userId, string $token): void
    {

        $tokenRow = $this->connectTokenRepository->getToken($token, EConnectTokenType::MINECRAFT);
        if ($tokenRow === null)
        {
            throw new BadRequestException('Neexistující token.');
        }


        $tokenObject = $this->userConnectTokenMapper->mapToken($tokenRow);
        if ($tokenObject->isUsed())
        {
            throw new BadRequestException('Neplatný token.');
        }

        if ($tokenObject->hasExpired())
        {
            throw new BadRequestException('Platnost tokenu vypršela. Vygenerujte ho, prosím, znovu.');
        }


        $uuid = $tokenObject->getData()['uuid'];
        $nick = $tokenObject->getData()['nick'];
        $this->minecraftAccountRepository->runInTransaction(function () use ($userId, $tokenRow, $tokenObject, $uuid, $nick) {

            $tokenRow->update([
                UserConnectTokenRepository::COLUMN_USER_ID => $userId
            ]);

            $this->minecraftAccountRepository->save([
                UserMinecraftAccountRepository::COLUMN_USER_ID => $userId,
                UserMinecraftAccountRepository::COLUMN_NICK => $nick,
                UserMinecraftAccountRepository::COLUMN_UUID => hex2bin($uuid),
            ]);

            $this->connectTokenRepository->markAsUsed($tokenObject->getId());
        });
    }

    /**
     * @param string $type
     * @param array $data
     * @return string
     * @throws JsonException
     */
    public function createToken(string $type, array $data): string
    {

        $token = Keygen::alphanum(16)->generate(function($key) {
            return implode('-', str_split(mb_strtoupper($key), 4));
        });

        $this->connectTokenRepository->save([
            UserConnectTokenRepository::COLUMN_TOKEN => $token,
            UserConnectTokenRepository::COLUMN_TYPE => $type,
            UserConnectTokenRepository::COLUMN_DATA => Json::encode($data),
            UserConnectTokenRepository::COLUMN_VALID_TO => (new DateTime())->add(new DateInterval('PT30M'))
        ]);

        return $token;
    }

    public function isConnectedToMinecraft(string $uuid): bool
    {
        return $this->minecraftAccountRepository->getAccountByUUID($uuid) !== null;
    }

    /**
     * @throws BadRequestException
     */
    public function disconnect(int $userId, string $type)
    {
        switch ($type)
        {
            default:
                throw new BadRequestException('Připojení tohoto typu neexistuje.');
            case EConnectTokenType::MINECRAFT:
                return $this->minecraftAccountRepository->getAccountByUserId($userId)
                    ?->update([
                        UserMinecraftAccountRepository::COLUMN_NOT_DELETED => null
                    ]);
        }
    }
}