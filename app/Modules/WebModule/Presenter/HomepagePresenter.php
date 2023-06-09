<?php

declare(strict_types=1);

namespace App\Modules\WebModule\Presenter;

use App\Modules\WebModule\Component\RecentArticlesListing\IRecentArticlesListingFactory;
use App\Modules\WebModule\Component\RecentArticlesListing\RecentArticlesListing;
use App\Modules\WebModule\Component\ServerListing\IServerListingFactory;
use App\Modules\WebModule\Component\ServerListing\ServerListing;
use App\Modules\WebModule\Presenter\Base\BasePresenter;

class HomepagePresenter extends BasePresenter
{

    /**
     * Class constructor
     * @param IRecentArticlesListingFactory $recentArticlesGridFactory
     * @param IServerListingFactory $serverListingFactory
     */
    public function __construct(
        private IRecentArticlesListingFactory $recentArticlesGridFactory,
        private IServerListingFactory $serverListingFactory,
    )
    {
        parent::__construct();
    }

    /**
     * @return void
     */
    public function actionDefault(): void
    {

    }

    /**
     * Creates RecentArticlesListing component
     * @return RecentArticlesListing
     */
    public function createComponentRecentArticlesListing(): RecentArticlesListing
    {
        return $this->recentArticlesGridFactory->create();
    }

    /**
     * Creates ServerListing component
     * @return ServerListing
     */
    public function createComponentServerListing(): ServerListing {
        return $this->serverListingFactory->create();
    }
}
