<?php

namespace App\Modules\WebModule\Component\RecentArticlesListing;

use App\Component\BaseComponent;
use App\Repository\Primary\ArticleRepository;

class RecentArticlesListing extends BaseComponent
{

    private const LIMIT = 3;

    /**
     * Class constructor
     */
    public function __construct(
        private ArticleRepository $articleRepository
    )
    {
    }

    /**
     * Render component
     * @return void
     */
    public function render(): void
    {

        $this->template->articles = $this->articleRepository->getMostRecentArticles(self::LIMIT)->fetchAll();

        parent::render();
    }
}

interface IRecentArticlesListingFactory
{
    public function create(): RecentArticlesListing;
}