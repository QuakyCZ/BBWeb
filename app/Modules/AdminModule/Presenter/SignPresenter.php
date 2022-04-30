<?php

namespace App\Modules\AdminModule\Presenter;


use App\Modules\AdminModule\Component\SignInForm\ISignInFormFactory;
use App\Modules\AdminModule\Presenter\Base\BasePresenter;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;
use Nette\Application\AbortException;
use Nette\Application\UI\Presenter;

class SignPresenter extends BasePresenter
{
    private ISignInFormFactory $signInFormFactory;

    public function __construct(ISignInFormFactory $signInFormFactory)
    {
        parent::__construct();
        $this->signInFormFactory = $signInFormFactory;
    }

    /**
     * @throws AbortException
     */
    public function actionIn(): void
    {
        if($this->getUser()->isLoggedIn())
        {
            $this->redirect('Default:');
        }
    }

    /**
     * @throws AbortException
     */
    public function actionOut(): void
    {
        if(!$this->getUser()->isLoggedIn())
        {
            $this->redirect('Default:');
        }

        $this->user->logout(true);
        $this->redirect('Default:');
    }


    public function createComponentSignInForm() {
        return $this->signInFormFactory->create();
    }
}