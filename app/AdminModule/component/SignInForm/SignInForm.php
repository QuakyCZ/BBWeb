<?php

namespace App\AdminModule\component\SignInForm;

use App\Component\BaseComponent;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;

class SignInForm extends BaseComponent
{
    public function createComponentForm(): Form
    {
        $form = new Form();
        $form->addEmail('email', 'Email')->setRequired(TRUE);
        $form->addPassword('password', 'Heslo')->setRequired(TRUE);
        $form->addSubmit('submit', 'Přihlásit se');
        $form->addProtection();
        $form->onSuccess[] = [$this, 'onFormSuccess'];
        return $form;

    }

    public function onFormSuccess(Form $form, $values) {
        try
        {
            $this->presenter->user->login($values['email'], $values['password']);
            $this->presenter->redirect('Default:');
        } catch (AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }
}

interface SignInFormFactory {
    public function create(): SignInForm;
}