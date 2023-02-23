<?php

namespace App\Modules\AdminModule\Component\User;

use App\Component\BaseDataGrid;
use App\Repository\Primary\RoleRepository;
use App\Repository\Primary\UserRepository;
use App\Repository\Primary\UserRoleRepository;
use Contributte\Translation\Exceptions\InvalidArgument;
use Contributte\Translation\Translator;
use Nette\Application\AbortException;
use Nette\ComponentModel\IContainer;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;
use Ublaboo\DataGrid\Exception\DataGridColumnStatusException;
use Ublaboo\DataGrid\Exception\DataGridException;

class UserGrid extends BaseDataGrid
{
    private UserRepository $userRepository;
    private RoleRepository $roleRepository;
    private UserRoleRepository $userRoleRepository;

    public function __construct(
        IContainer $parent,
        UserRepository $userRepository,
        UserRoleRepository $userRoleRepository,
        RoleRepository $roleRepository,
        Translator $translator
    ) {
        $this->userRepository = $userRepository;
        $this->userRoleRepository = $userRoleRepository;
        $this->roleRepository = $roleRepository;
        parent::__construct($parent, 'userGrid', $translator);
    }

    /**
     * @return Selection
     */
    protected function getSelection(): Selection
    {
        return $this->userRepository->findAll();
    }

    /**
     * @throws DataGridException
     */
    public function createGrid(): void
    {
        // username
        $this->grid->addColumnText('username', 'Username')
            ->setSortable()
            ->setFilterText();

        // email
        $this->grid->addColumnText('email', 'Email')
            ->setFilterText();

        // roles
        $this->grid->addColumnText('roles', 'Role')
            ->setRenderer(function (ActiveRow $item) {
                $roles = $this->userRoleRepository->getUsersRoleNames($item->id);
                foreach ($roles as $id => $role) {
                    $roles[$id] = $this->translator->translate('admin.users.roles.'.$role);
                }
                return implode(", ", $roles);
            });

        // created
        $this->grid->addColumnDateTime('created', 'Vytvořeno')
            ->setSortable();

        // active
        $this->grid->addColumnText('active', 'Aktivní')
            ->setTemplateEscaping(false)
            ->setAlign('center')
            ->setReplacement([
                0 => '<i class="fa-regular fa-square text-danger"></i>',
                1 => '<i class="fa-regular fa-square-check text-success"></i>'
            ])->setFilterSelect([
                null => '-',
                1 => 'Ano',
                0 => 'Ne',
            ]);

        $this->grid->addAction('activate', '', 'activate!')
            ->setTitle('Aktivovat')
            ->setIcon('thumbs-up')
            ->setClass('btn btn-outline-primary ajax')
            ->setRenderCondition(function (ActiveRow $row) {
                return $row->id !== $this->grid->presenter->user->id && $row->active == false;
            })
            ->setConfirmation(new StringConfirmation('Opravdu chcete aktivovat uživatele %s?', 'username'));

        $this->grid->addAction('deactivate', '', 'deactivate!')
            ->setTitle('Deaktivovat')
            ->setIcon('thumbs-down')
            ->setClass('btn btn-outline-primary ajax')
            ->setRenderCondition(function (ActiveRow $row) {
                return $row->id !== $this->grid->presenter->user->id && $row->active == true;
            })
            ->setConfirmation(new StringConfirmation('Opravdu chcete deaktivovat uživatele %s?', 'username'));

        $this->grid->addAction('edit', '', 'Users:edit', ['id' => 'id'])
            ->setTitle('Editovat')
            ->setIcon('pen')
            ->setClass('btn btn-outline-warning');

        $this->grid->addAction('delete', '', 'delete!')
            ->setTitle('Odstranit')
            ->setIcon('trash')
            ->setClass('btn btn-danger ajax')
            ->setRenderCondition(function (ActiveRow $row) {
                return $row->id !== $this->grid->presenter->user->id;
            })
            ->setConfirmation(new StringConfirmation('Opravdu chcete smazat uživatele %s?', 'username'));

        $this->grid->addToolbarButton('Users:add', $this->translator->translate('admin.users.add'))
            ->setIcon('plus')
            ->setClass('btn btn-success');
    }
}
