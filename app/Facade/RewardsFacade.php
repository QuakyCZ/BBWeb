<?php

namespace App\Facade;

use App\Modules\AdminModule\Component\Rewards\RewardsFormValues;
use App\Repository\Rewards\RewardCommandRepository;
use App\Repository\Rewards\RewardPermissionRepository;
use App\Repository\Rewards\RewardRepository;
use App\Repository\Rewards\RewardServerRepository;
use App\Repository\Rewards\ServerRepository;
use Nette\Database\Table\ActiveRow;

class RewardsFacade
{

    /**
     * Class constructor
     * @param RewardRepository $rewardRepository
     * @param RewardServerRepository $rewardServerRepository
     * @param RewardPermissionRepository $rewardPermissionRepository
     * @param RewardCommandRepository $rewardCommandRepository
     */
    public function __construct(
        private RewardRepository $rewardRepository,
        private RewardServerRepository $rewardServerRepository,
        private RewardPermissionRepository $rewardPermissionRepository,
        private RewardCommandRepository $rewardCommandRepository
    ) {
    }

    /**
     * Saves reward to db
     * @param RewardsFormValues $values
     * @param int|null $id
     * @return ActiveRow saved row
     */
    public function saveReward(RewardsFormValues $values, ?int $id): ActiveRow {
        return $this->rewardRepository->runInTransaction(function () use ($values, $id) {

            // Delete all previous mappings
            if ($id !== null) {
                $this->rewardPermissionRepository->deleteAllForReward($id);
                $this->rewardCommandRepository->deleteAllForReward($id);
                $this->rewardServerRepository->deleteAllForReward($id);
            }

            /** @var ActiveRow $reward */
            $reward = $this->rewardRepository->save([
                RewardRepository::COLUMN_ID => $id,
                RewardRepository::COLUMN_NAME => $values->name,
                RewardRepository::COLUMN_COOLDOWN => $values->cooldown
            ]);

            // servers
            foreach ($values->server_ids as $serverId) {
                $this->rewardServerRepository->save([
                    RewardServerRepository::COLUMN_SERVER_ID => $serverId,
                    RewardServerRepository::COLUMN_REWARD_ID => $reward[RewardRepository::COLUMN_ID]
                ]);
            }

            // commands
            foreach ($values->commands as $command) {
                $this->rewardCommandRepository->save([
                    RewardCommandRepository::COLUMN_REWARD_ID => $reward[RewardRepository::COLUMN_ID],
                    RewardCommandRepository::COLUMN_COMMAND => $command[RewardCommandRepository::COLUMN_COMMAND],
                    RewardCommandRepository::COLUMN_ORDER => $command[RewardCommandRepository::COLUMN_ORDER]
                ]);
            }

            // permissions
            foreach ($values->permissions as $permission) {
                $this->rewardPermissionRepository->save([
                    RewardPermissionRepository::COLUMN_REWARD_ID => $reward[RewardRepository::COLUMN_ID],
                    RewardPermissionRepository::COLUMN_PERMISSION => $permission[RewardPermissionRepository::COLUMN_PERMISSION]
                ]);
            }

            return $reward;
        });
    }

    /**
     * @param int $rewardId
     * @return RewardsFormValues|null
     */
    public function getFormValues(int $rewardId): ?RewardsFormValues {

        $reward = $this->rewardRepository->getRow($rewardId);
        if ($reward === null)
            return null;

        $serversIds = $reward->related(RewardServerRepository::TABLE_NAME)
            ->fetchPairs(
                RewardServerRepository::COLUMN_SERVER_ID,
                RewardServerRepository::COLUMN_SERVER_ID
            );

        $rewardValues = new RewardsFormValues();
        $rewardValues->name = $reward[RewardRepository::COLUMN_NAME];
        $rewardValues->cooldown = $reward[RewardRepository::COLUMN_COOLDOWN];
        $rewardValues->server_ids = $serversIds;
        $rewardValues->permissions = [];
        $rewardValues->commands = [];

        $permissions = $reward->related(RewardPermissionRepository::TABLE_NAME)->fetchAll();

        foreach ($permissions as $perm) {
            $rewardValues->permissions[] = [
                RewardPermissionRepository::COLUMN_PERMISSION => $perm[RewardPermissionRepository::COLUMN_PERMISSION]
            ];
        }

        $commands = $reward->related(RewardCommandRepository::TABLE_NAME)->fetchAll();

        foreach ($commands as $command) {
            $rewardValues->commands[] = [
                RewardCommandRepository::COLUMN_COMMAND => $command[RewardCommandRepository::COLUMN_COMMAND],
                RewardCommandRepository::COLUMN_ORDER => $command[RewardCommandRepository::COLUMN_ORDER]
            ];
        }

        return $rewardValues;
    }
}