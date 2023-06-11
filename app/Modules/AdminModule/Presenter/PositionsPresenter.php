<?php

namespace App\Modules\AdminModule\Presenter;

use App\Modules\AdminModule\Component\Position\IPositionFormFactory;
use App\Modules\AdminModule\Component\Position\IPositionGridFactory;
use App\Modules\AdminModule\Component\Position\PositionForm;
use App\Modules\AdminModule\Component\Position\PositionGrid;
use Ublaboo\DataGrid\DataGrid;

class PositionsPresenter extends Base\BasePresenter
{

    private ?int $id = null;

    /**
     * @param IPositionFormFactory $positionFormFactory
     * @param IPositionGridFactory $positionGridFactory
     */
    public function __construct(
        private readonly IPositionFormFactory $positionFormFactory,
        private readonly IPositionGridFactory $positionGridFactory,
    )
    {
        parent::__construct();
    }

    public function actionEdit(int $id): void {
        $this->id = $id;
    }

    public function createComponentPositionForm(): PositionForm {
        return $this->positionFormFactory->create($this->id);
    }

    public function createComponentPositionGrid(): DataGrid {
        return $this->positionGridFactory->create($this)->create();
    }
}