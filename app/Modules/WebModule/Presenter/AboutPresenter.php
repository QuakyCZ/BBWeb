<?php

namespace App\Modules\WebModule\Presenter;


use App\Modules\WebModule\Component\Team\ITeamFactory;
use App\Modules\WebModule\Component\Team\Team;
use App\Modules\WebModule\Presenter\Base\BasePresenter;
use App\Repository\UserDetailsRepository;
use App\Repository\UserRoleRepository;
use Nette\Application\UI\Presenter;
use Nette\Localization\Translator;

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
    public function createComponentTeam(): Team {
        return $this->teamFactory->create();
    }
}