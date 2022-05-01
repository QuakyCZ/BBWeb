<?php

namespace App\Modules\AdminModule\Component\SignInForm;

use App\Component\BaseComponent;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;

class SignInForm extends BaseComponent
{

    public function __construct
    (
        private ?string $defaultRoute = 'Default:',
        private ?string $returnKey = null,
    )
    {
    }

    public function createComponentForm(): Form
    {
        $form = new Form();
        $form->addEmail('email', 'Email')->setRequired('%label je povinný údaj');
        $form->addPassword('password', 'Heslo')->setRequired('%label je povinný údaj');
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

            if ($this->returnKey !== null)
            {
                $this->presenter->restoreRequest($this->returnKey);
            }

            $this->presenter->redirect($this->defaultRoute);
        } catch (AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }
}

