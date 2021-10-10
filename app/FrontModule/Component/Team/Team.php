<?php

namespace App\FrontModule\Component;

use App\Component\BaseComponent;
use App\Repository\UserDetailsRepository;
use App\Repository\UserRoleRepository;

class Team extends BaseComponent {

    private UserRoleRepository $userRoleRepository;
    private UserDetailsRepository $userDetailsRepository;

    public function __construct(
        UserRoleRepository $userRoleRepository,
        UserDetailsRepository $userDetailsRepository
    ) {
        $this->userRoleRepository = $userRoleRepository;
        $this->userDetailsRepository = $userDetailsRepository;
    }

    public function render(): void {

        $rows = $this->userRoleRepository->getForAboutTeamListing([1, 7])->fetchAll();
        bdump($rows);
        $roles = [];
        foreach ($rows as $row) {
            $roles[$row->role_id]['id'] = $row->role_id;
            $roles[$row->role_id]['name'] = $this->presenter->translator->translate('front.about.roles.'.$row->name);

            $roles[$row->role_id]['users'][] = [
                'id'=>$row['user_id'],
                'username'=>$row['username'],
                'details'=>$this->userDetailsRepository->getDetails($row['user_id'],'minecraft_nick, position')->fetch()->toArray()
            ];
        }
        bdump($roles);
        $this->template->roles = $roles;

        parent::render();
    }
}

interface ITeamFactory {
    public function create(): Team;
}