<?php

namespace App\Modules\AdminModule\Presenter;


use App\Modules\AdminModule\Component\SignInForm\ISignInFormFactory;
use App\Modules\AdminModule\Component\SignInForm\SignInForm;
use App\Modules\AdminModule\Component\SignUpForm\ISignUpFormFactory;
use App\Modules\AdminModule\Component\SignUpForm\SignUpForm;
use App\Modules\AdminModule\Presenter\Base\BasePresenter;
use App\Modules\ApiModule\Model\User\UserFacade;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;
use Nette\Application\AbortException;
use Nette\Application\UI\Presenter;
use Tracy\Debugger;

class SignPresenter extends BasePresenter
{
    public function __construct(
        private ISignInFormFactory $signInFormFactory,
        private ISignUpFormFactory $signUpFormFactory,
        private UserFacade $userFacade
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
    public function actionUp(): void
    {
        $this->actionIn();
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

    /**
     * @throws AbortException
     */
    public function actionVerify(int $userId, string $token): void
    {
        try {
            $verifiedUser = $this->userFacade->verifyUserToken($userId, $token);
            if ($verifiedUser === null)
            {
                $this->flashMessage('Neplatný token.', 'warning');
                $this->redirect('Sign:in');
            }
        } catch (AbortException $exception) {
            throw $exception;
        }
        catch (\Exception $exception) {
            Debugger::log($exception, 'exception');
            $this->flashMessage('Nastala chyba.', 'warning');
            $this->redirect('Sign:in');
        }
    }

    /**
     * @return SignInForm
     */
    public function createComponentSignInForm(): SignInForm {
        return $this->signInFormFactory->create('Default:', $this->getParameter('returnKey'));
    }

    /**
     * @return SignUpForm
     */
    public function createComponentSignUpForm(): SignUpForm {
        return $this->signUpFormFactory->create();
    }
}