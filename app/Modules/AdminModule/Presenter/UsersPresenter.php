<?php

namespace App\Modules\AdminModule\Presenter;

use App\Modules\AdminModule\Component\User\IUserFormFactory;
use App\Modules\AdminModule\Component\User\IUserGridFactory;
use App\Modules\AdminModule\Component\User\UserForm;
use App\Modules\AdminModule\Component\User\UserGrid;
use App\Repository\UserRepository;
use App\Repository\UserRoleRepository;

class UsersPresenter extends Base\BasePresenter {

    private UserRepository $userRepository;
    private UserRoleRepository $userRoleRepository;

    private IUserFormFactory $userFormFactory;
    private IUserGridFactory $userGridFactory;

    public function __construct(
        UserRepository     $userRepository,
        UserRoleRepository $userRoleRepository,
        IUserFormFactory   $userFormFactory,
        IUserGridFactory   $userGridFactory
    ) {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->userRoleRepository = $userRoleRepository;
        $this->userFormFactory = $userFormFactory;
        $this->userGridFactory= $userGridFactory;
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

    /**
     * @return UserForm
     */
    public function createComponentUserForm(): UserForm {
        return $this->userFormFactory->create();
    }

    public function createComponentUserGrid(): UserGrid {
        return $this->userGridFactory->create();
    }

    public function handleDelete(int $id): void {
        $this->userRepository->setNotDeletedNull($id);
        $this->flashMessage("Uživatel byl smazán.");
        if ($this->isAjax()) {
            $this->redrawControl('flashes');
            $this['userGrid']->reload();
        } else {
            $this->redirect('this');
        }
    }

    public function handleActivate($id): void {
        $this->userRepository->setActive($id,true);
        if ($this->isAjax()) {
            $this->redrawControl('flashes');
            $this['userGrid']->reload();
        } else {
            $this->redirect('this');
        }
    }

    public function handleDeactivate($id): void {
        $this->userRepository->setActive($id,false);
        if ($this->isAjax()) {
            $this->redrawControl('flashes');
            $this['userGrid']->reload();
        } else {
            $this->redirect('this');
        }
    }
}