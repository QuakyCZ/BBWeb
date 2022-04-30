<?php

namespace App\Modules\ApiModule\Model\Player;

use App\Repository\DungeonEscape\PlayerRepository;

class PlayerFacade
{

    public function __construct
    (
        private PlayerRepository $playerRepository,
        private PlayerMapper $playerMapper
    )
    {
    }

    public function getByName(string $name): ?Player
    {
        $row = $this->playerRepository->findBy(['name' => $name], true)->fetch();
        if ($row === null)
        {
            return null;
        }

        return $this->playerMapper->mapPlayer($row);
    }

    public function getByUuid(string $uuid): ?Player
    {
        $row = $this->playerRepository->findBy(['uuid' => $uuid], true)->fetch();
        if ($row === null)
        {
            return null;
        }

        return $this->playerMapper->mapPlayer($row);
    }
}