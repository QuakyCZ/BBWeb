<?php

namespace App\Modules\AdminModule\Component\User;

use App\Component\BaseComponent;
use App\Repository\RoleRepository;
use App\Repository\UserDetailsRepository;
use App\Repository\UserRepository;
use App\Repository\UserRoleRepository;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Security\Passwords;
use Nette\Utils\ArrayHash;

class UserForm extends BaseComponent {

    private RoleRepository $roleRepository;
    private UserRepository $userRepository;
    private UserRoleRepository $userRoleRepository;
    private UserDetailsRepository $userDetailsRepository;
    private Passwords $passwords;

    public function __construct(
        RoleRepository $roleRepository,
        UserRepository $userRepository,
        UserRoleRepository $userRoleRepository,
        UserDetailsRepository $userDetailsRepository,
        Passwords $passwords
    ) {
        $this->roleRepository = $roleRepository;
        $this->userRepository = $userRepository;
        $this->userRoleRepository = $userRoleRepository;
        $this->userDetailsRepository = $userDetailsRepository;
        $this->passwords = $passwords;
    }

    /**
     * @return Form
     */
    public function createComponentForm(): Form {
        $form = new Form();
        $form->addProtection();

        $form->addText('username', 'Uživatelské jméno')
            ->setRequired('%label is required')
            ->addRule(Form::MIN_LENGTH, 'Minimální délka jsou %d znaky.', 3);

        $form->addText('minecraft_nick', 'Minecraft Nick')
            ->setRequired('%label is required');

        $form->addText('firstname', 'Jméno');

        $form->addText('lastname', 'Příjmení');

        $form->addEmail('email', 'Email')
            ->setRequired('%label is required');

        $password = $form->addPassword('password', 'Heslo');

        $form->addPassword('passwordCheck', 'Kontrola hesla')
            ->addConditionOn($password, $form::FILLED)
            ->setRequired()
            ->endCondition();

        $form->addMultiSelect('roles', 'Role', $this->roleRepository->getDataForSelect());

        $form->addText('position', 'Detail pozice');

        $form->addSubmit('submit', 'Registrovat');

        $form->onValidate[] = [$this, 'validateForm'];
        $form->onSuccess[] = [$this, 'completeForm'];

        return $form;
    }

    /**
     * @param Form $form
     */
    public function validateForm(Form $form) {

        if($this->userRepository->findByEmail($form->values['email']) !== null)
        {
            $form->addError('Uživatel s tímto emailem již existuje.');
        }

        if($this->userRepository->findByUsername($form->values['username']) !== null)
        {
            $form->addError('Uživatel s tímto jménem již existuje.');
        }

        if($form->values['password'] !== $form->values['passwordCheck'])
        {
            $form->addError('Hesla se neshodují.');
        }

        if(!empty($form->values['roles']) &&
            (!$this->presenter->user->isLoggedIn() || !$this->presenter->user->isInRole('ADMIN'))
        ) {
            $form->addError('Nemáš oprávnění na nastavování rolí.');
        }
    }

    /**
     * @param Form $form
     * @param ArrayHash $data
     * @throws AbortException
     */
    public function completeForm(Form $form, ArrayHash $data) {
        $user = [
            'username'=>$data['username'],
            'email'=>$data['email'],
            'password'=>$this->passwords->hash($data['password'])
        ];

        $userDetails = [
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'minecraft_nick' => $data['minecraft_nick'],
            'position' => $data['position']
        ];

        if($this->presenter->user->isLoggedIn()) {
            $user['created_user_id'] = $this->presenter->user->id;
        }

        $this->userRepository->database->beginTransaction();

        try {
            $newUser = $this->userRepository->save($user);
            $userDetails['user_id'] = $newUser['id'];

            $this->userDetailsRepository->save($userDetails);

            foreach ($data['roles'] as $role) {
                $this->userRoleRepository->save([
                    'user_id' => $newUser['id'],
                    'role_id' => $role,
                    'created_user_id' => $this->presenter->user->id
                ]);
            }

            $this->userRepository->database->commit();
        }
        catch (\Exception $e) {
            $this->userRepository->database->rollBack();
            $form->addError('Něco se nepovedlo.');
            return;
        }
        $this->flashMessage('Uživatel byl přidán.');
        $this->presenter->redirect('Users:');
    }
}

interface IUserFormFactory {
    public function create(): UserForm;
}