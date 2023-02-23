<?php

namespace App\Modules\ApiModule\Model\PlayerStatistics;

class PlayerStatisticsFacade
{
    /**
     * @param PlayerStatisticsMapper $playerStatisticsMapper
     * @param PlayerStatisticsRepository $playerStatisticsRepository
     */
    public function __construct(
        private PlayerStatisticsMapper $playerStatisticsMapper,
        private PlayerStatisticsRepository $playerStatisticsRepository,
    ) {
    }

    /**
     * @param int $statisticsId
     * @return PlayerStatistics|null
     */
    public function getPlayerStatistics(int $statisticsId): ?PlayerStatistics
    {
        $row = $this->playerStatisticsRepository->getById($statisticsId);
        if ($row === null) {
            return null;
        }
        return $this->playerStatisticsMapper->mapPlayerStatistics($row);
    }
}
