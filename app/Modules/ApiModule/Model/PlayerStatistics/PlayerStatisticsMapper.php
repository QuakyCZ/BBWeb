<?php

namespace App\Modules\ApiModule\Model\PlayerStatistics;

use Nette\Database\Table\ActiveRow;

class PlayerStatisticsMapper
{
    public function mapPlayerStatistics(ActiveRow $row): PlayerStatistics
    {
        return new PlayerStatistics(
            $row['id'],
            $row['normalGamesPlayed'],
            $row['coopGamesPlayed'],
            $row['battleRoyaleGamesPlayed'],
            $row['singleWins'],
            $row['coopWins'],
            $row['battleRoyaleWins'],
            $row['deaths'],
            $row['mobKills'],
            $row['bossKills'],
            $row['playerKills'],
            $row['highestScore'],
            $row['totalDealtDamage'],
            $row['totalTakenDamage'],
        );
    }
}
