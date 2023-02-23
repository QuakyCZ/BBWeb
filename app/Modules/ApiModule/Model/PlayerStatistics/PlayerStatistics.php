<?php

namespace App\Modules\ApiModule\Model\PlayerStatistics;

class PlayerStatistics
{
    public function __construct(
        private int $id,
        private int $normalGamesPlayed,
        private int $coopGamesPlayed,
        private int $battleRoyaleGamesPlayed,
        private int $singleWins,
        private int $coopWins,
        private int $battleRoyaleWins,
        private int $deaths,
        private int $mobKills,
        private int $bossKills,
        private int $playerKills,
        private float $highestScore,
        private float $totalDealtDamage,
        private float $totalTakenDamage,
    ) {
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getNormalGamesPlayed(): int
    {
        return $this->normalGamesPlayed;
    }

    /**
     * @return int
     */
    public function getCoopGamesPlayed(): int
    {
        return $this->coopGamesPlayed;
    }

    /**
     * @return int
     */
    public function getBattleRoyaleGamesPlayed(): int
    {
        return $this->battleRoyaleGamesPlayed;
    }

    /**
     * @return int
     */
    public function getSingleWins(): int
    {
        return $this->singleWins;
    }

    /**
     * @return int
     */
    public function getCoopWins(): int
    {
        return $this->coopWins;
    }

    /**
     * @return int
     */
    public function getBattleRoyaleWins(): int
    {
        return $this->battleRoyaleWins;
    }

    /**
     * @return int
     */
    public function getDeaths(): int
    {
        return $this->deaths;
    }

    /**
     * @return int
     */
    public function getMobKills(): int
    {
        return $this->mobKills;
    }

    /**
     * @return int
     */
    public function getBossKills(): int
    {
        return $this->bossKills;
    }

    /**
     * @return int
     */
    public function getPlayerKills(): int
    {
        return $this->playerKills;
    }

    /**
     * @return float
     */
    public function getHighestScore(): float
    {
        return $this->highestScore;
    }

    /**
     * @return float
     */
    public function getTotalDealtDamage(): float
    {
        return $this->totalDealtDamage;
    }

    /**
     * @return float
     */
    public function getTotalTakenDamage(): float
    {
        return $this->totalTakenDamage;
    }


    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
           'id' => $this->id,
           'normalGamesPlayed' => $this->normalGamesPlayed,
           'coopGamesPlayed' => $this->coopGamesPlayed,
           'battleRoyaleGamesPlayed' => $this->battleRoyaleGamesPlayed,
           'singleWins' => $this->singleWins,
           'coopWins' => $this->coopWins,
           'battleRoyaleWins' => $this->battleRoyaleWins,
           'deaths' => $this->deaths,
           'mobKills' => $this->mobKills,
           'bossKills' => $this->bossKills,
           'playerKills' => $this->playerKills,
           'highestScore' => $this->highestScore,
           'totalDealtDamage' => $this->totalDealtDamage,
           'totalTakenDamage' => $this->totalTakenDamage,
        ];
    }
}
