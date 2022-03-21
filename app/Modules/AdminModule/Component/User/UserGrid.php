<?php

namespace App\Modules\AdminModule\Component\User;

use _PHPStan_76800bfb5\Symfony\Component\Console\Question\ConfirmationQuestion;
use App\Component\BaseComponent;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Repository\UserRoleRepository;
use Contributte\Translation\Exceptions\InvalidArgument;
use Contributte\Translation\Translator;
use Nette\ComponentModel\IContainer;
use Nette\Database\Table\ActiveRow;
use Ublaboo\DataGrid\Column\Action\Confirmation\CallbackConfirmation;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\DataSource\DibiFluentDataSource;
use Ublaboo\DataGrid\Exception\DataGridColumnStatusException;
use Ublaboo\DataGrid\Exception\DataGridException;
use function _PHPStan_76800bfb5\React\Promise\reduce;

class UserGrid extends DataGrid
{
    private UserRepository $userRepository;
    private RoleRepository $roleRepository;
    private UserRoleRepository $userRoleRepository;

    public function __construct(
        UserRepository $userRepository,
        UserRoleRepository $userRoleRepository,
        RoleRepository $roleRepository,
        Translator $translator
    )
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->userRoleRepository = $userRoleRepository;
        $this->roleRepository = $roleRepository;
        $this->translator = $translator;
    }

    /**
     * @throws DataGridException|InvalidArgument
     * @throws DataGridColumnStatusException
     */
    public function render(): void
    {
        $this->setDataSource($this->userRepository->findAll());

        // username
        $this->addColumnText('username', 'Username')
            ->setFilterText()
            ->setCondition(function ($fluent, $value) {
                $fluent->where("username LIKE '%?%'", $value);
            });

        // email
        $this->addColumnText('email', 'Email')
            ->setFilterText();

        // roles
        $this->addColumnText('roles', 'Role')
            ->setRenderer(function (ActiveRow $item) {
                $roles = $this->userRoleRepository->getUsersRoleNames($item->id);
                foreach ($roles as $id => $role) {
                    $roles[$id] = $this->translator->translate('admin.users.roles.'.$role);
                }
                return implode(", ", $roles);
            });

        /*$this->addFilterMultiSelect('roles', 'Role:',$this->roleRepository->getDataForSelect())
                ->setCondition(function ($fluent, $value) {

                });*/

        // created
        $this->addColumnDateTime('created', 'Vytvořeno');

        // active
        $this->addColumnText('active', 'Aktivní')
            ->setTemplateEscaping(false)
            ->setAlign('center')
            ->setReplacement([
                0 => '<i class="fa-regular fa-square text-danger"></i>',
                1 => '<i class="fa-regular fa-square-check text-success"></i>'
            ]);

        $this->addAction('activate', '', 'activate!')
            ->setTitle('Aktivovat')
            ->setIcon('thumbs-up')
            ->setClass('btn btn-warning ajax')
            ->setRenderCondition(function (ActiveRow $row) {
                return $row->id !== $this->presenter->user->id && $row->active == false;
            })
            ->setConfirmation(new StringConfirmation('Opravdu chcete aktivovat uživatele %s?', 'username'));

        $this->addAction('deactivate', '', 'deactivate!')
            ->setTitle('Deaktivovat')
            ->setIcon('thumbs-down')
            ->setClass('btn btn-warning ajax')
            ->setRenderCondition(function (ActiveRow $row) {
                return $row->id !== $this->presenter->user->id && $row->active == true;
            })
            ->setConfirmation(new StringConfirmation('Opravdu chcete deaktivovat uživatele %s?', 'username'));

        $this->addAction('delete', '', 'delete!')
            ->setTitle('Odstranit')
            ->setIcon('trash')
            ->setClass('btn btn-danger ajax')
            ->setRenderCondition(function (ActiveRow $row) {
                return $row->id !== $this->presenter->user->id;
            })
            ->setConfirmation(new StringConfirmation('Opravdu chcete smazat uživatele %s?', 'username'));

        $this->addToolbarButton('Users:add', $this->translator->translate('admin.users.add'))
            ->setIcon('plus')
            ->setClass('btn btn-success');

        parent::render();
    }

}

interface IUserGridFactory
{
    /**
     * @return UserGrid
     */
    public function create(): UserGrid;
}