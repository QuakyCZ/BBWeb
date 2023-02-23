<?php

namespace App\Modules\WebModule\Presenter;

use App\Modules\WebModule\Component\Team\ITeamFactory;
use App\Modules\WebModule\Component\Team\Team;
use App\Modules\WebModule\Presenter\Base\BasePresenter;

class AboutPresenter extends BasePresenter
{
    private ITeamFactory $teamFactory;

    public function __construct(
        ITeamFactory $teamFactory
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
}
