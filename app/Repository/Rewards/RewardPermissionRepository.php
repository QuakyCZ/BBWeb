<?php

namespace App\Repository\Rewards;

use Nette\Database\Table\Selection;

class RewardPermissionRepository extends RewardsRepository
{
    public const TABLE_NAME = "reward_permission";

    public const COLUMN_ID = "id";
    public const COLUMN_REWARD_ID = "reward_id";
    public const COLUMN_PERMISSION = "permission";
    public const COLUMN_NOT_DELETED = "not_deleted";

    protected string $tableName = self::TABLE_NAME;


    /**
     * Get permissions for reward.
     * @param int $rewardId
     * @return Selection
     */
    public function getForReward(int $rewardId): Selection {
        return $this->findBy([
            self::COLUMN_REWARD_ID => $rewardId
        ]);
    }


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