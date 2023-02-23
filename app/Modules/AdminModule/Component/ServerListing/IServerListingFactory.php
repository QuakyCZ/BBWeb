<?php

namespace App\Modules\AdminModule\Component\ServerListing;

interface IServerListingFactory
{
    public function create(): ServerListing;
}
