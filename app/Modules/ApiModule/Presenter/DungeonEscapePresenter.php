<?php

namespace App\Modules\ApiModule\Presenter;

use App\Modules\ApiModule\Model\Player\PlayerFacade;
use App\Modules\ApiModule\Model\Player\PlayerMapper;
use App\Modules\ApiModule\Model\PlayerStatistics\PlayerStatisticsFacade;
use App\Repository\DungeonEscape\PlayerRepository;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\Responses\JsonResponse;

class DungeonEscapePresenter extends \App\Modules\WebModule\Presenter\Base\BasePresenter
{
    public function __construct(
        private PlayerFacade $playerFacade,
        private PlayerStatisticsFacade $playerStatisticsFacade,
    ) {
        parent::__construct();
    }


    /**
     * @param string|null $uuid
     * @param string|null $name
     * @return void
     * @throws AbortException
     * @throws BadRequestException
     */
    public function actionPlayer(?string $uuid, ?string $name): void
    {
        $player = null;
        if ($uuid !== null) {
            $player = $this->playerFacade->getByUuid(hex2bin($uuid));
        } elseif ($name !== null) {
            $player = $this->playerFacade->getByName($name);
        } else {
            $this->sendJson(['error' => 'Name or UUID must be specified.']);
        }

        if ($player === null) {
            $this->sendJson(['error' => 'Player was not found.']);
        }

        $this->sendJson(['player' => $player->toArray()]);
    }

    /**
     * @throws AbortException
     */
    public function actionStatistics(?int $id): void
    {
        if ($id === null) {
            $this->sendJson(['error' => 'ID must be specified.']);
        }

        $stats = $this->playerStatisticsFacade->getPlayerStatistics($id);
        if ($stats === null) {
            $this->sendJson(['error' => 'Statistics were not found.']);
        }

        $this->sendJson(['player_statistics' => $stats->toArray()]);
    }
}
