<?php

namespace App\Modules\AdminModule\Presenter;

use App\Modules\AdminModule\Component\Rewards\IRewardsFormFactory;
use App\Modules\AdminModule\Component\Rewards\IRewardsGridFactory;
use App\Modules\AdminModule\Component\Rewards\RewardsForm;
use App\Modules\AdminModule\Component\Rewards\RewardsGrid;
use Ublaboo\DataGrid\DataGrid;

class RewardsPresenter extends Base\BasePresenter
{

    private ?int $id = null;

    /**
     * @param IRewardsFormFactory $rewardsFormFactory
     * @param IRewardsGridFactory $rewardsGridFactory
     */
    public function __construct(
        private IRewardsFormFactory $rewardsFormFactory,
        private IRewardsGridFactory $rewardsGridFactory
    )
    {
    }

    /**
     * @return void
     */
    public function actionDefault(): void {

    }

    /**
     * @return void
     */
    public function actionAdd(): void {

    }

    /**
     * @param int $id
     * @return void
     */
    public function actionEdit(int $id): void {
        $this->id = $id;
    }


    /**
     * @return RewardsForm
     */
    public function createComponentRewardsForm(): RewardsForm {
        return $this->rewardsFormFactory->create($this->id);
    }

    /**
     * @return DataGrid
     */
    public function createComponentRewardsGrid(): DataGrid {
        return $this->rewardsGridFactory->create($this)->create();
    }
}