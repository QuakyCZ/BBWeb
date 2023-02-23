<?php

namespace App\Modules\AdminModule\Component\ForgottenPasswordForm;

use App\Facade\MailFacade;
use App\Repository\Primary\UserRepository;
use Nette\Application\BadRequestException;
use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

class ForgottenPasswordForm extends \App\Component\BaseComponent
{
    public function __construct(
        private UserRepository $userRepository,
        private MailFacade $mailFacade,
    ) {
    }

    public function createComponentForm(): Form
    {
        $form = new Form();
        $form->addEmail('email', 'Email')
            ->setHtmlAttribute('placeholder', 'Email')
            ->setRequired();

        $form->addReCaptcha();
        $form->addSubmit('submit', 'Odeslat');
        $form->onSuccess[] = [$this, 'submitForm'];
        return $form;
    }


    /**
     * @param Form $form
     * @param ArrayHash $values
     * @return void
     * @throws BadRequestException
     * @throws ForbiddenRequestException
     * @throws \Nette\Application\AbortException
     * @throws \Nette\Application\UI\InvalidLinkException
     */
    public function submitForm(Form $form, ArrayHash $values): void
    {
        $xUrl = $form->getHttpData($form::DATA_TEXT, 'x_url');

        if (empty($xUrl) || $xUrl !== "nospam") {
            throw new ForbiddenRequestException();
        }

        $time = (string) time();

        $email = $values[UserRepository::COLUMN_EMAIL];

        $user = $this->userRepository->findByEmail($email);

        if ($user === null) {
            throw new BadRequestException();
        }

        $token = $this->userRepository->getVerificationToken($user, $time);
        $link = $this->presenter->link('//Sign:resetPassword', [
            'id' => $user[UserRepository::COLUMN_ID],
            'email' => $email,
            'token' => $token,
            't' => $time
        ]);

        $this->mailFacade->sendResetPasswordMail($email, $link);
        $this->presenter->redirect('Sign:forgottenPasswordSent');
    }
}
