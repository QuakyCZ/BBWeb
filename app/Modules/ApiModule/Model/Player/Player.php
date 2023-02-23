<?php

namespace App\Modules\ApiModule\Model\Player;

use JetBrains\PhpStorm\ArrayShape;

class Player
{
    public function __construct(
        private ?int $id,
        private string $name,
        private string $uuid,
        private ?int $statisticsId,
        private bool $deleted = false
    ) {
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return int|null
     */
    public function getStatisticsId(): ?int
    {
        return $this->statisticsId;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * @return array
     */
    #[ArrayShape(['id' => "int|null", 'name' => "string", 'uuid' => "string", 'statisticsId' => "int|null"])]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'uuid' => bin2hex($this->uuid),
            'statisticsId' => $this->statisticsId
        ];
    }
}
