<?php

namespace App\AdminModule\Component;

use App\Repository\ServerRepository;

class ServerListing extends \App\Component\BaseComponent {

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

interface IServerListingFactory {
    public function create(): ServerListing;
}