<?php

namespace App\Modules\ApiModule\Model\User;

use Nette\Utils\DateTime;

class UserConnectToken
{
    public function __construct
    (
        private int $id,
        private ?int $userId,
        private string $type,
        private string $token,
        private array|\stdClass $data,
        private bool $used,
        private DateTime $validTo
    )
    {
    }

    /**
     * @return array|\stdClass
     */
    public function getData(): array|\stdClass
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return ?int
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return bool
     */
    public function isUsed(): bool
    {
        return $this->used;
    }

    /**
     * @return DateTime
     */
    public function getValidTo(): DateTime
    {
        return $this->validTo;
    }

    /**
     * @return bool
     */
    public function hasExpired(): bool
    {
        return (new DateTime()) > $this->validTo;
    }
}