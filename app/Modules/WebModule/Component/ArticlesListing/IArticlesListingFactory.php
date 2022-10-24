<?php

namespace App\Modules\WebModule\Component\ArticlesListing;

interface IArticlesListingFactory
{
    public const DEFAULT_MAX_PER_PAGE = 10;

    /**
     * @param int $page
     * @param int $maxPerPage
     * @return ArticlesListing
     */
    public function create(int $page = 0, int $maxPerPage = self::DEFAULT_MAX_PER_PAGE): ArticlesListing;
}