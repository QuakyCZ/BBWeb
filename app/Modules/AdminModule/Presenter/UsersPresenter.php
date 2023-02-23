<?php

namespace App\Modules\AdminModule\Presenter;

use App\Enum\EFlashMessageType;
use App\Modules\AdminModule\Component\User\IUserFormFactory;
use App\Modules\AdminModule\Component\User\IUserGridFactory;
use App\Modules\AdminModule\Component\User\UserForm;
use App\Modules\AdminModule\Component\User\UserGrid;
use App\Repository\Primary\UserRepository;
use App\Repository\Primary\UserRoleRepository;
use Nette\Application\AbortException;
use Nette\Database\Table\ActiveRow;
use Nette\Localization\ITranslator;
use Symfony\Component\Translation\Translator;
use Tracy\Debugger;
use Tracy\ILogger;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

class UsersPresenter extends Base\BasePresenter
{
    private ?int $id = null;

    private UserRepository $userRepository;
    private UserRoleRepository $userRoleRepository;

    private IUserFormFactory $userFormFactory;
    private IUserGridFactory $userGridFactory;

    private ITranslator $translator;

    public function __construct(
        UserRepository     $userRepository,
        UserRoleRepository $userRoleRepository,
        IUserFormFactory   $userFormFactory,
        IUserGridFactory   $userGridFactory,
        ITranslator         $translator,
    ) {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->userRoleRepository = $userRoleRepository;
        $this->userFormFactory = $userFormFactory;
        $this->userGridFactory= $userGridFactory;
        $this->translator = $translator;
    }

    public function actionDefault()
    {
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

    public function actionEdit(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return UserForm
     */
    public function createComponentUserForm(): UserForm
    {
        return $this->userFormFactory->create($this->id);
    }

    /**
     * @return DataGrid
     */
    public function createComponentUserGrid(): DataGrid
    {
        return $this->userGridFactory->create($this)->create();
    }

    /**
     * @param int $id
     * @return void
     * @throws AbortException
     */
    public function handleDelete(int $id): void
    {
        $this->userRepository->setNotDeletedNull($id);
        $this->flashMessage("Uživatel byl smazán.");
        if ($this->presenter->isAjax()) {
            $this->redrawControl('flashes');
            $this['userGrid']->reload();
        } else {
            $this->redirect('this');
        }
    }

    /**
     * @param $id
     * @return void
     * @throws AbortException
     */
    public function handleActivate($id): void
    {
        try {
            $this->userRepository->setActive($id, true);
            $this->flashMessage('Uživatel byl aktivován.');
        } catch (\Exception $exception) {
            Debugger::log($exception);
            $this->flashMessage($this->translator->translate('common.error'), EFlashMessageType::ERROR);
        }
        if ($this->presenter->isAjax()) {
            $this->redrawControl('flashes');
            $this['userGrid']->reload();
        } else {
            $this->redirect('this');
        }
    }

    /**
     * @param $id
     * @return void
     * @throws AbortException
     */
    public function handleDeactivate($id): void
    {
        try {
            $this->userRepository->setActive($id, false);
            $this->flashMessage('Uživatel byl deaktivován.');
        } catch (\Exception $exception) {
            Debugger::log($exception, ILogger::EXCEPTION);
            $this->flashMessage($this->translator->translate('common.error'), EFlashMessageType::ERROR);
        }
        if ($this->presenter->isAjax()) {
            $this->redrawControl('flashes');
            $this['userGrid']->reload();
        } else {
            $this->redirect('this');
        }
    }
}
