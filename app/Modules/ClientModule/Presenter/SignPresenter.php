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
     * @return void
     * @throws AbortException
     */
    public function actionIn(): void
    {
        if ($this->getUser()->isLoggedIn())
        {
            $returnKey = $this->getParameter('returnKey');
            if ($returnKey !== null)
            {
                $this->restoreRequest($returnKey);
            }
            $this->redirect('Dashboard:');
        }
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