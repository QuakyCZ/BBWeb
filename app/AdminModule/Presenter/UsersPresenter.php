<?php

namespace App\AdminModule\Presenter;

use App\AdminModule\component\AddUserForm\IAddUserFormFactory;
use App\Repository\UserRepository;
use App\Repository\UserRoleRepository;

class UsersPresenter extends Base\BasePresenter {

    private UserRepository $userRepository;
    private UserRoleRepository $userRoleRepository;

    private IAddUserFormFactory $addUserFormFactory;

    public function __construct(
        UserRepository $userRepository,
        UserRoleRepository $userRoleRepository,
        IAddUserFormFactory $addUserFormFactory
    ) {
        $this->userRepository = $userRepository;
        $this->userRoleRepository = $userRoleRepository;
        $this->addUserFormFactory = $addUserFormFactory;
    }

    public function actionDefault() {
        $users = $this->userRepository->getForListing()->fetchAll();
        $result = [];
        foreach ($users as $user) {
            $resUser = $user->toArray();
            $roles = $this->userRoleRepository->getUsersRoleNames($user['id']);
            $resUser['roles'] = $roles;
            $result[] = $resUser;
        }

        $this->template->users = $result;
    }

    public function createComponentAddUserForm() {
        return $this->addUserFormFactory->create();
    }

}