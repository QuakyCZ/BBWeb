<?php

namespace App\Modules\AdminModule\Presenter;

use App\Modules\AdminModule\Component\ServerListing\IServerListingFactory;
use App\Modules\AdminModule\Component\ServerListing\ServerListing;

class ServersPresenter extends Base\BasePresenter {

    private IServerListingFactory $serverListingFactory;

    /**
     * @param IServerListingFactory $serverListingFactory
     */
    public function __construct(IServerListingFactory $serverListingFactory) {
        parent::__construct();
        $this->serverListingFactory = $serverListingFactory;
    }

    /**
     * @return ServerListing
     */
    public function createComponentServerListing(): ServerListing {
        return $this->serverListingFactory->create();
    }
}