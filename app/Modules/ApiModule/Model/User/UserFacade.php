<?php

namespace App\Modules\ApiModule\Model\User;

use App\Enum\EConnectTokenType;
use App\Facade\MailFacade;
use App\Repository\Primary\UserConnectTokenRepository;
use App\Repository\Primary\UserDetailsRepository;
use App\Repository\Primary\UserMinecraftAccountRepository;
use App\Repository\Primary\UserRepository;
use App\Repository\Primary\UserRoleRepository;
use DateInterval;
use Keygen\Generator;
use Keygen\Keygen;
use Nette\Application\BadRequestException;
use Nette\Application\LinkGenerator;
use Nette\Application\UI\InvalidLinkException;
use Nette\Database\Table\ActiveRow;
use Nette\Mail\Mailer;
use Nette\NotImplementedException;
use Nette\Security\Passwords;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Throwable;
use Tracy\Debugger;

class UserFacade
{
    public function __construct
    (
        private UserRepository $userRepository,
        private UserRoleRepository $userRoleRepository,
        private UserMinecraftAccountRepository $minecraftAccountRepository,
        private UserConnectTokenRepository $connectTokenRepository,
        private UserConnectTokenMapper $userConnectTokenMapper,
        private MailFacade $mailFacade,
        private Passwords $passwords,
        private LinkGenerator $linkGenerator
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

    /**
     * @param ArrayHash $values
     * @throws \PDOException|\Exception|Throwable
     * @return ?ActiveRow
     */
    public function register(ArrayHash $values): mixed
    {
        $verificationToken = Keygen::alphanum(30)->generate();

        return $this->userRepository->runInTransaction(function () use ($values, $verificationToken) {

            $row = $this->userRepository->save([
                UserRepository::COLUMN_USERNAME => $values[UserRepository::COLUMN_USERNAME],
                UserRepository::COLUMN_EMAIL => $values[UserRepository::COLUMN_EMAIL],
                UserRepository::COLUMN_PASSWORD => $this->passwords->hash($values[UserRepository::COLUMN_PASSWORD]),
                UserRepository::COLUMN_VERIFICATION_TOKEN => $verificationToken
            ]);

            $userId = $row[UserRepository::COLUMN_ID];

            $this->userRoleRepository->save([
                UserRoleRepository::COLUMN_USER_ID => $userId,
                UserRoleRepository::COLUMN_ROLE_ID => 9 // User
            ]);

            $this->sendVerificationEmail($values[UserRepository::COLUMN_EMAIL], $userId, $verificationToken);

            return $row;
        });
    }

    /**
     * @param string $email
     * @param string $userId
     * @param string $token
     * @return void
     * @throws InvalidLinkException
     */
    public function sendVerificationEmail(string $email, string $userId, string $token): void
    {
        $link = $this->linkGenerator->link('Client:Sign:verify', [
            'userId' => $userId,
            'token' => $token,
        ]);

        $this->mailFacade->sendMail($email, 'Ověření emailu', __DIR__.'/../../../../Mail/VerificationMail.latte', [
            'verificationUrl' => $link
        ]);
    }

    /**
     * @param string $email
     * @return ActiveRow|null
     */
    public function getByEmail(string $email): ?ActiveRow
    {
        return $this->userRepository->findByEmail($email);
    }

    /**
     * @param string $username
     * @return ActiveRow|null
     */
    public function getByUsername(string $username): ?ActiveRow
    {
        return $this->userRepository->findByUsername($username);
    }

    /**
     * @param int $userId
     * @param string $token
     * @return ActiveRow|null
     */
    public function verifyUserToken(int $userId, string $token): ?ActiveRow
    {
        return $this->userRepository->verifyUser($userId, $token);
    }
}