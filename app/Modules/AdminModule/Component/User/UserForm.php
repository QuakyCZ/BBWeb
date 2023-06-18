<?php

namespace App\Modules\AdminModule\Component\User;

use App\Component\BaseComponent;
use App\Modules\ApiModule\Model\User\UserFacade;
use App\Repository\Primary\RoleRepository;
use App\Repository\Primary\UserDetailsRepository;
use App\Repository\Primary\UserRepository;
use App\Repository\Primary\UserRoleRepository;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Security\Passwords;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;
use Tracy\ILogger;

class UserForm extends BaseComponent
{
    private ?int $id;
    private RoleRepository $roleRepository;
    private UserFacade $userFacade;
    private UserRepository $userRepository;
    private UserRoleRepository $userRoleRepository;
    private UserDetailsRepository $userDetailsRepository;
    private Passwords $passwords;

    public function __construct(
        ?int $id,
        RoleRepository $roleRepository,
        UserFacade $userFacade,
        UserRepository $userRepository,
        UserRoleRepository $userRoleRepository,
        UserDetailsRepository $userDetailsRepository,
        Passwords $passwords
    ) {
        $this->id = $id;
        $this->roleRepository = $roleRepository;
        $this->userFacade = $userFacade;
        $this->userRepository = $userRepository;
        $this->userRoleRepository = $userRoleRepository;
        $this->userDetailsRepository = $userDetailsRepository;
        $this->passwords = $passwords;
    }


    /**
     * @throws BadRequestException
     */
    public function render(): void
    {
        if ($this->id !== null) {
            $user = $this->userFacade->getById($this->id);
            if ($user === null) {
                throw new BadRequestException('Uživatel nebyl nalezen.', 400);
            }

            $defaults = $user->toArray();

            /** @var Form $form */
            $form = $this['form'];

            $roles = $this->userRoleRepository->getUsersRoles($this->id)->fetchPairs(UserRoleRepository::COLUMN_ROLE_ID, UserRoleRepository::COLUMN_ROLE_ID);

            $defaults['role_ids'] = $roles;

            $details = $this->userDetailsRepository->getDetails($this->id)->fetch();

            if ($details !== null) {
                foreach ($details->toArray() as $key => $value) {
                    $defaults[$key] = $value;
                }
            }

            $form->setDefaults($defaults);
        }

        $this->template->editedUserId = $this->id;

        parent::render();
    }

    /**
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $form = new \App\Form\Form();
        $form->addProtection();

        $form->addText('username', 'Uživatelské jméno')
            ->setRequired('%label is required')
            ->addRule(Form::MIN_LENGTH, 'Minimální délka jsou %d znaky.', 3);

        $form->addEmail('email', 'Email')
            ->setRequired('%label is required')
            ->addRule(Form::EMAIL, 'Neplatný formát emailu.');

        $form->addText('firstname', 'Jméno');

        $form->addText('lastname', 'Příjmení');


        if ($this->presenter->user->isInRole('ADMIN') || $this->presenter->user->getId() === $this->id) {
            $password = $form->addPassword('password', 'Heslo');
            $password->addCondition(Form::FILLED)
                ->addRule(Form::MIN_LENGTH, 'Minimální délka hesla je 8 znaků.', 8)
                ->endCondition();

            $form->addPassword('passwordCheck', 'Kontrola hesla')
                ->addConditionOn($password, $form::FILLED)
                ->setRequired()
                ->endCondition();
        }


        if ($this->presenter->user->isInRole('ADMIN')) {
            $roles = $this->roleRepository->getDataForSelect();
            $form->addMultiSelect2('role_ids', 'Role', $roles);
        } else {
            $form->addHidden('role_ids');
        }

        $form->addText('position', 'Detail pozice');

        $form->addSubmit('submit', 'Registrovat');

        $form->onValidate[] = [$this, 'validateForm'];
        $form->onSuccess[] = [$this, 'completeForm'];

        return $form;
    }

    /**
     * @param Form $form
     */
    public function validateForm(Form $form): void
    {
        $user = $this->userRepository->findByEmail($form->values['email']);
        if ($user !== null && ($this->id === null xor $user[UserRepository::COLUMN_ID] !== $this->id)) {
            $form->addError('Uživatel s tímto emailem již existuje.');
        }

        $user = $this->userRepository->findByUsername($form->values['username']);
        if ($user !== null && ($this->id === null xor $user[UserRepository::COLUMN_ID] !== $this->id)) {
            $form->addError('Uživatel s tímto jménem již existuje.');
        }

        if ($form->values['password'] !== $form->values['passwordCheck']) {
            $form->addError('Hesla se neshodují.');
        }

        if (!empty($form->values['role_ids']) &&
            (!$this->presenter->user->isLoggedIn() || !$this->presenter->user->isInRole('ADMIN'))
        ) {
            $form->addError('Nemáš oprávnění na nastavování rolí.');
        }
    }

    /**
     * @param Form $form
     * @param ArrayHash $data
     * @throws AbortException
     * @throws BadRequestException
     */
    public function completeForm(Form $form, ArrayHash $data): void
    {
        $sessionUser = $this->getPresenter()?->getUser();

        $user = [
            'username'=>$data['username'],
            'email'=>$data['email'],
        ];

        if (!empty($data['password'])) {
            $user['password'] = $this->passwords->hash($data['password']);
        }

        $userDetails = [
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'position' => $data['position']
        ];

        if ($this->id !== null) {
            $existingUser = $this->userFacade->getById($this->id);
            if ($existingUser === null) {
                throw new BadRequestException('Uživatel nebyl nalezen.');
            }

            $user['id'] = $existingUser[UserRepository::COLUMN_ID];
            $user['changed_user_id'] = $sessionUser->getId();
            $user['changed'] = new \DateTime();

            $details = $this->userDetailsRepository->getDetails($this->id)->fetch();
            if ($details !== null) {
                $userDetails['id'] = $details['id'];
                $userDetails['changed_user_id'] = $sessionUser->getId();
                $userDetails['changed'] = new \DateTime();
            } else {
                $userDetails['created_user_id'] = $sessionUser->getId();
            }
        } else {
            $user['created_user_id'] = $this->presenter->user->id;
        }

        $this->userRepository->database->beginTransaction();

        try {
            $newUser = $this->userRepository->save($user);

            $userDetails['user_id'] = $newUser['id'];

            $this->userDetailsRepository->save($userDetails);

            $this->userRoleRepository->dropUserRoles($this->id);

            foreach ($data['role_ids'] as $role) {
                $this->userRoleRepository->save([
                    'user_id' => $newUser['id'],
                    'role_id' => $role,
                    'created_user_id' => $this->presenter->user->id
                ]);
            }

            $this->userRepository->database->commit();
        } catch (\Exception $e) {
            Debugger::log($e, ILogger::EXCEPTION);
            $this->userRepository->database->rollBack();
            $form->addError('Něco se nepovedlo.');
            return;
        }
        $this->presenter->flashMessage('Uživatel byl přidán.');
        $this->presenter->redirect('Users:');
    }
}
