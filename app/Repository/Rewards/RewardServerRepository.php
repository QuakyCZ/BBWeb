<?php

namespace App\Repository\Rewards;

class RewardServerRepository extends RewardsRepository
{
    public const TABLE_NAME = "reward_server";
    public const COLUMN_REWARD_ID = "reward_id";
    public const COLUMN_SERVER_ID = "server_id";

    public const COLUMN_NOT_DELETED = "not_deleted";

    protected string $tableName = self::TABLE_NAME;

    /**
     * Deletes all records for reward
     * @param int $rewardId
     * @return void
     */
    public function deleteAllForReward(int $rewardId): void {
        $this->findBy([
            self::COLUMN_REWARD_ID => $rewardId
        ])->update([
            self::COLUMN_NOT_DELETED => null
        ]);
    }
}