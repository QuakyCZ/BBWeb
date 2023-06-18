<?php

namespace App\Modules\WebModule\Presenter;

use App\Repository\Primary\ServerRepository;

class ServersPresenter extends Base\BasePresenter {


    /**
     * @param ServerRepository $serverRepository
     */
    public function __construct(
        private ServerRepository $serverRepository
    )
    {
        parent::__construct();
    }

    /**
     * @return void
     */
    public function actionDefault(): void {
        $this->template->servers = $this->serverRepository->findBy([
            ServerRepository::COLUMN_SHOW => true
        ]);
    }
}