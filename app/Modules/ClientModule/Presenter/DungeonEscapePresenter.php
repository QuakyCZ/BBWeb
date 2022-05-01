<?php

namespace App\Modules\ClientModule\Presenter;

use App\Modules\ApiModule\Model\Player\PlayerFacade;
use App\Modules\ApiModule\Model\PlayerStatistics\PlayerStatisticsFacade;
use App\Modules\ApiModule\Model\User\UserFacade;
use Nette\InvalidStateException;

class DungeonEscapePresenter extends ClientPresenter
{
    /**
     * @param PlayerFacade $playerFacade
     * @param PlayerStatisticsFacade $playerStatisticsFacade
     */
    public function __construct
    (
        private PlayerFacade $playerFacade,
        private PlayerStatisticsFacade $playerStatisticsFacade
    )
    {
        parent::__construct();
    }


    public function actionDefault(): void
    {
        $player = $this->playerFacade->getByUserId($this->getUser()->getId());

        $minecraftConnected = $player !== null;

        $this->template->isMinecraftConnected = $minecraftConnected;

        if ($minecraftConnected)
        {
            $statistics = $this->playerStatisticsFacade->getPlayerStatistics($player->getId());
            if ($statistics === null)
            {
                throw new InvalidStateException();
            }
            $this->template->statistics = $statistics;
        }
    }
}