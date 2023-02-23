<?php

namespace App\Modules\ApiModule\Model\Player;

use App\Repository\DungeonEscape\PlayerRepository;
use App\Repository\Primary\UserMinecraftAccountRepository;

class PlayerFacade
{
    public function __construct(
        private PlayerRepository $playerRepository,
        private PlayerMapper $playerMapper,
        private UserMinecraftAccountRepository $minecraftAccountRepository
    ) {
    }

    /**
     * @param int $userId
     * @return Player|null
     */
    public function getByUserId(int $userId): ?Player
    {
        $minecraftAccount = $this->minecraftAccountRepository->getAccountByUserId($userId);
        if ($minecraftAccount === null) {
            return null;
        }
        return $this->getByUuid($minecraftAccount[UserMinecraftAccountRepository::COLUMN_UUID]);
    }

    public function getByName(string $name): ?Player
    {
        $row = $this->playerRepository->findBy(['name' => $name], true)->fetch();
        if ($row === null) {
            return null;
        }

        return $this->playerMapper->mapPlayer($row);
    }

    public function getByUuid(string $uuid): ?Player
    {
        $row = $this->playerRepository->getByUuid($uuid);

        if ($row === null) {
            return null;
        }

        return $this->playerMapper->mapPlayer($row);
    }
}
