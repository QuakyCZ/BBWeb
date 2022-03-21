<?php

namespace App\Modules\WebModule\Component\Team;

use App\Component\BaseComponent;
use App\Repository\RoleRepository;
use App\Repository\UserDetailsRepository;
use App\Repository\UserRoleRepository;
use Nette\Localization\Translator;

class Team extends BaseComponent {
    private RoleRepository $roleRepository;
    private UserRoleRepository $userRoleRepository;
    private UserDetailsRepository $userDetailsRepository;

    private Translator $translator;

    public function __construct(
        RoleRepository $roleRepository,
        UserRoleRepository $userRoleRepository,
        UserDetailsRepository $userDetailsRepository,
        Translator $translator
    ) {
        $this->roleRepository = $roleRepository;
        $this->userRoleRepository = $userRoleRepository;
        $this->userDetailsRepository = $userDetailsRepository;
        $this->translator = $translator;
    }

    public function render(): void {

        $result = $this->userRoleRepository->getForAboutTeamListing([1]);
        bdump($result);
        $this->template->roles = $result;

        parent::render();
    }
}

interface ITeamFactory {
    public function create(): Team;
}