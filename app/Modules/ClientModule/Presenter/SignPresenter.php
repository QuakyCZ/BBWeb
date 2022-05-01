<?php

namespace App\Modules\ClientModule\Presenter;

use App\Modules\AdminModule\Component\SignInForm\ISignInFormFactory;
use App\Modules\AdminModule\Component\SignInForm\SignInForm;
use Nette\Application\AbortException;

class SignPresenter extends ClientPresenter
{
    public function __construct
    (
        private ISignInFormFactory $signInFormFactory
    )
    {
        parent::__construct();
    }

    /**
     * @throws AbortException
     */
    public function actionOut(): void
    {
        if ($this->getUser()->isLoggedIn())
        {
            $this->getUser()->logout(true);
        }
        $this->presenter->redirect('Sign:in');
    }


    /**
     * @return SignInForm
     */
    public function createComponentSignInForm(): SignInForm
    {
        return $this->signInFormFactory->create('Dashboard:', $this->getParameter('returnKey'));
    }
}