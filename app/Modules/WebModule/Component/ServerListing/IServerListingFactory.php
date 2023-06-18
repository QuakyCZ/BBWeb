<?php

namespace App\Modules\WebModule\Component\ServerListing;

interface IServerListingFactory
{
    /**
     * @return ServerListing
     */
    public function create(): ServerListing;
}