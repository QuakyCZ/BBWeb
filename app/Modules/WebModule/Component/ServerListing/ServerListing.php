<?php

namespace App\Modules\WebModule\Component\ServerListing;

use App\Component\BaseComponent;
use App\Repository\Primary\ServerRepository;

class ServerListing extends BaseComponent
{
    public function __construct(
        private ServerRepository $serverRepository
    )
    {
    }

    /**
     * @return void
     */
    public function render(): void
    {
        $this->template->servers = $this->serverRepository->findBy([
            ServerRepository::COLUMN_SHOW => true
        ]);
        parent::render();
    }
}