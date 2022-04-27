<?php

namespace App\Modules\AdminModule\Component\ServerListing;

use App\Component\BaseComponent;
use App\Repository\ServerRepository;

class ServerListing extends BaseComponent {

    private ServerRepository $serverRepository;

    /**
     * @param ServerRepository $serverRepository
     */
    public function __construct
    (
        ServerRepository $serverRepository
    ) {

        $this->serverRepository = $serverRepository;
    }


    public function render(): void {
        $this->template->servers = $this->serverRepository->getAll();

        parent::render();
    }
}
