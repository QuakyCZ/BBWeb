<?php

namespace App\Modules\WebModule\Component\Positions;

interface IPositionsListingFactory
{
    /**
     * @return PositionsListing
     */
    public function create(): PositionsListing;
}