<?php

namespace App\Modules\AdminModule\Presenter;

use App\Enum\EFlashMessageType;
use App\Modules\AdminModule\Component\ForgottenPasswordForm\ForgottenPasswordForm;
use App\Modules\AdminModule\Component\ForgottenPasswordForm\IForgottenPasswordFormFactory;
use App\Modules\AdminModule\Component\ResetPasswordForm\IResetPasswordFormFactory;
use App\Modules\AdminModule\Component\ResetPasswordForm\ResetPasswordForm;
use App\Modules\AdminModule\Component\SignInForm\ISignInFormFactory;
use App\Modules\AdminModule\Component\SignInForm\SignInForm;
use App\Modules\AdminModule\Component\SignUpForm\ISignUpFormFactory;
use App\Modules\AdminModule\Component\SignUpForm\SignUpForm;
use App\Modules\AdminModule\Presenter\Base\BasePresenter;
use App\Modules\ApiModule\Model\User\UserFacade;
use App\Repository\Primary\UserRepository;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Tracy\Debugger;

class SignPresenter extends BasePresenter
{
    public function __construct(
        private UserFacade $userFacade,
        private UserRepository $userRepository,
        private ISignInFormFactory $signInFormFactory,
        private ISignUpFormFactory $signUpFormFactory,
        private IForgottenPasswordFormFactory $forgottenPasswordFormFactory,
        private IResetPasswordFormFactory $resetPasswordFormFactory,
    ) {
        parent::__construct();
    }

    /**
     * @return void
     * @throws AbortException
     */
    public function actionIn(): void
    {
        if ($this->getUser()->isLoggedIn()) {
            $returnKey = $this->getParameter('returnKey');
            if ($returnKey !== null) {
                $this->restoreRequest($returnKey);
            }
            $this->redirect('Default:');
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
        if (!$this->getUser()->isLoggedIn()) {
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
            if ($verifiedUser === null) {
                $this->flashMessage('Neplatný token.', 'warning');
            }
            $this->flashMessage('Ověření proběhlo úspěšně', 'success');
            $this->redirect('Sign:in');
        } catch (AbortException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            Debugger::log($exception, 'exception');
            $this->flashMessage('Nastala chyba.', 'warning');
            $this->redirect('Sign:in');
        }
    }


    /**
     * @param int $id
     * @param string $email
     * @param string $token
     * @param int $t
     * @return void
     * @throws AbortException
     * @throws BadRequestException
     */
    public function actionResetPassword(int $id, string $email, string $token, int $t): void
    {
        if (time() - $t > 5*60) {
            $this->flashMessage('Platnost tokenu vypršela.', EFlashMessageType::ERROR);
            $this->redirect('Sign:forgottenPassword');
        }

        $user = $this->userRepository->findBy([
            UserRepository::COLUMN_ID => $id,
            UserRepository::COLUMN_EMAIL => $email,
        ])->fetch();

        if ($user === null) {
            throw new BadRequestException();
        }

        if (!$this->userRepository->checkVerificationToken($token, $user, $t)) {
            throw new BadRequestException();
        }

        /** @var ResetPasswordForm $form */
        $rpf = $this->getComponent('resetPasswordForm');

        /** @var Form $form */
        $form = $rpf->getComponent('form');

        $form->setDefaults([
            'id' => $id,
            'email' => $email,
            'token' => $token,
            't' => $t
        ]);
    }

    /**
     * @return SignInForm
     */
    public function createComponentSignInForm(): SignInForm
    {
        return $this->signInFormFactory->create('Default:', $this->getParameter('returnKey'));
    }

    /**
     * @return SignUpForm
     */
    public function createComponentSignUpForm(): SignUpForm
    {
        return $this->signUpFormFactory->create();
    }


    public function createComponentForgottenPasswordForm(): ForgottenPasswordForm
    {
        return $this->forgottenPasswordFormFactory->create();
    }

    /**
     * @return ResetPasswordForm
     */
    public function createComponentResetPasswordForm(): ResetPasswordForm
    {
        return $this->resetPasswordFormFactory->create();
    }
}
