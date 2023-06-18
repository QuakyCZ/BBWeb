<?php

namespace App\Modules\WebModule\Presenter;

use App\Modules\WebModule\Component\Positions\IPositionsListingFactory;
use App\Modules\WebModule\Component\Positions\PositionsListing;
use App\Modules\WebModule\Component\Team\ITeamFactory;
use App\Modules\WebModule\Component\Team\Team;
use App\Modules\WebModule\Presenter\Base\BasePresenter;

class AboutPresenter extends BasePresenter
{
    public function __construct(
        private ITeamFactory $teamFactory,
        private IPositionsListingFactory $positionsListingFactory,
    ) {
        parent::__construct();
        $this->teamFactory = $teamFactory;
    }



    /**
     * @return Team
     */
    public function createComponentTeam(): Team
    {
        return $this->teamFactory->create();
    }

    /**
     * @return PositionsListing
     */
    public function createComponentPositionsListing(): PositionsListing
    {
        return $this->positionsListingFactory->create();
    }
}
