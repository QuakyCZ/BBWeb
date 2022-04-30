<?php

namespace App\Modules\AdminModule\Component\SignInForm;

use App\Component\BaseComponent;
use Nette\Application\AbortException;
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

    /**
     * @throws AbortException
     */
    public function onFormSuccess(Form $form, $values): void
    {
        try
        {
            $this->presenter->user->login($values['email'], $values['password']);

            $key = $this->presenter->getParameter('returnKey');
            if ($key !== null)
            {
                $this->presenter->restoreRequest($key);
            }

            $this->presenter->redirect('Default:');
        } catch (AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }
}

