<?php

namespace App\Modules\AdminModule\Component\ResetPasswordForm;

use App\Component\BaseComponent;
use App\Enum\EFlashMessageType;
use App\Repository\Primary\UserRepository;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\BaseControl;
use Nette\Security\Passwords;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;

class ResetPasswordForm extends BaseComponent
{

    public function __construct(
        private UserRepository $userRepository,
        private Passwords $passwords,
    )
    {
    }

    public function createComponentForm(): Form {
        $form = new Form();
        $password = $form->addPassword('password', 'Heslo')
            ->setHtmlAttribute('placeholder', 'Heslo')
            ->addRule($form::MIN_LENGTH, 'Minimální délka hesla je 8 znaků.', 8)
            ->addRule(function (BaseControl $input) {
                $value = $input->getValue();
                return preg_match('/[A-Za-z]/', $value) && preg_match('/\d/', $value);
            }, 'Heslo musí obsahovat alespoň jedno písmeno a alespoň jednu číslici.')
            ->setRequired('%label je povinný údaj.');

        $form->addPassword('password_check', 'Kontrola hesla')
            ->setHtmlAttribute('placeholder', 'Heslo znovu')
            ->setRequired('%label je povinný údaj.')
            ->addCondition($form::FILLED)
            ->addRule(function (BaseControl $input) use ($password) {
                return $input->getValue() === $password->getValue();
            }, 'Hesla se neshodují.')
            ->endCondition();

        $form->addHidden('id');
        $form->addHidden('email');
        $form->addHidden('token');
        $form->addHidden('t');

        $form->addReCaptcha();

        $form->addSubmit('save', 'Uložit');

        $form->onSuccess[] = [$this, 'saveForm'];

        return $form;
    }


    /**
     * @param Form $form
     * @param ArrayHash $values
     * @return void
     * @throws AbortException
     * @throws ForbiddenRequestException
     */
    public function saveForm(Form $form, ArrayHash $values): void {

        $xUrl = $form->getHttpData($form::DATA_TEXT, 'x_url');

        if (empty($xUrl) || $xUrl !== "nospam")
        {
            throw new ForbiddenRequestException();
        }

        if (time() - $values['t'] > 5 * 60) {
            $this->presenter->flashMessage('Platnost ověřovacího kódu vypršela.', EFlashMessageType::ERROR);
            $this->presenter->redirect('Sign:forgottenPassword');
        }

        $password = $values['password'];

        $user = $this->userRepository->findBy([
            UserRepository::COLUMN_ID => $values[UserRepository::COLUMN_ID],
            UserRepository::COLUMN_EMAIL => $values[UserRepository::COLUMN_EMAIL],
        ])->fetch();

        if ($user === null || !$this->userRepository->checkVerificationToken($values['token'], $user, $values['t'])) {
            $form->addError('Neplatný požadavek.');
            return;
        }

        try {
            $user->update([
                UserRepository::COLUMN_PASSWORD => $this->userRepository->getPasswordHash($password)
            ]);

            $this->presenter->flashMessage('Heslo bylo úspěšně změněno.', EFlashMessageType::SUCCESS);
            $this->presenter->redirect('Sign:in');
        } catch (\PDOException $exception) {
            Debugger::log($exception);
            $form->addError('Při zpracování požadavku nastala neznámá chyba.');
        }
    }
}

