<?php

namespace App\Modules\WebModule\Component\Team;

use App\Component\BaseComponent;
use App\Repository\RoleRepository;
use App\Repository\SettingsRepository;
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

        $data = $this->userRoleRepository->getForAboutTeamListing(['ADMIN'])->fetchAll();

        $result = [];

        foreach ($data as $row)
        {
            $result[$row['id']]['role'] = $this->translator->translate('front.about.roles.'.$row['name']);
            $result[$row['id']]['members'][] = [
                'minecraft_nick' => $row['minecraft_nick'],
                'position' => $row['position']
            ];
        }

        $this->template->roles = $result;

        parent::render();
    }
}

interface ITeamFactory {
    public function create(): Team;
}