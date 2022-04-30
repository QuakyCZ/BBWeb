<?php

namespace App\Modules\ApiModule\Model\PlayerStatistics;

class PlayerStatisticsFacade
{
    public function __construct
    (
        private PlayerStatisticsMapper $playerStatisticsMapper,
        private PlayerStatisticsRepository $playerStatisticsRepository,
    )
    {
    }

    public function getPlayerStatistics(int $statisticsId): ?PlayerStatistics
    {
        $row = $this->playerStatisticsRepository->findBy(['id' => $statisticsId], true)->fetch();
        if ($row === null)
        {
            return null;
        }
        return $this->playerStatisticsMapper->mapPlayerStatistics($row);
    }
}