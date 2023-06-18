<?php

namespace App\Modules\AdminModule\Presenter;

use App\Modules\AdminModule\Component\Server\IServerFormFactory;
use App\Modules\AdminModule\Component\Server\IServerGridFactory;
use App\Modules\AdminModule\Component\Server\ServerForm;
use App\Modules\AdminModule\Presenter\Base\BasePresenter;
use Ublaboo\DataGrid\DataGrid;

class ServersPresenter extends BasePresenter
{

    private ?int $id = null;

    /**
     * @param IServerFormFactory $serverFormFactory
     * @param IServerGridFactory $serverGridFactory
     */
    public function __construct(
        private IServerFormFactory $serverFormFactory,
        private IServerGridFactory $serverGridFactory
    )
    {
        parent::__construct();
    }

    public function actionEdit(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return DataGrid
     */
    public function createComponentServerGrid(): DataGrid
    {
        return $this->serverGridFactory->create($this)->create();
    }

    /**
     * @return ServerForm
     */
    public function createComponentServerForm(): ServerForm
    {
        return $this->serverFormFactory->create($this->id);
    }
}
