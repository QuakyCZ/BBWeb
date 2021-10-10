<?php

namespace App\AdminModule\Presenter;

use App\AdminModule\Component\IServerListingFactory;

class ServersPresenter extends Base\BasePresenter {

    private IServerListingFactory $serverListingFactory;

    public function __construct(IServerListingFactory $serverListingFactory) {
        $this->serverListingFactory = $serverListingFactory;
    }

    public function createComponentServerListing() {
        return $this->serverListingFactory->create();
    }
}