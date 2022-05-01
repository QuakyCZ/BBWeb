<?php

namespace App\Modules\AdminModule\Component\SignUpForm;

use App\Modules\ApiModule\Model\User\UserFacade;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\BaseControl;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;

class SignUpForm extends \App\Component\BaseComponent
{
    public function __construct
    (
        private UserFacade $userFacade
    )
    {
    }

    public function createComponentForm(): Form
    {
        $form = new Form();
        $form->addProtection();

        $form->addText('username', 'Uživatelské jméno')
            ->setHtmlAttribute('placeholder', 'Uživatelské jméno')
            ->setRequired('%label je povinný údaj.')
            ->addRule(Form::MIN_LENGTH, 'Minimální délka jsou %d znaky.', 3);

        $form->addEmail('email', 'Email')
            ->setHtmlAttribute('placeholder', 'Email')
            ->setRequired('%label je povinný údaj.');

        $password = $form->addPassword('password', 'Heslo')
            ->setHtmlAttribute('placeholder', 'Heslo')
            ->addRule($form::MIN_LENGTH, 'Minimální délka hesla je 8 znaků.', 8)
            ->addRule(function (BaseControl $input) {
                $value = $input->getValue();
                return preg_match('/[A-Za-z]/', $value) && preg_match('/\d/', $value);
            }, 'Heslo musí obsahovat alespoň jedno písmeno a alespoň jednu číslici.')
            ->setRequired('%label je povinný údaj.');

        $form->addPassword('passwordCheck', 'Kontrola hesla')
            ->setHtmlAttribute('placeholder', 'Heslo znovu')
            ->setRequired('%label je povinný údaj.')
            ->addCondition($form::FILLED)
                ->addRule(function (BaseControl $input) use ($password) {
                    return $input->getValue() === $password->getValue();
                }, 'Hesla se neshodují.')
            ->endCondition();

        $form->addReCaptcha('recaptcha', '', true, '')->setRequired('Potvrďte, že nejste robot.');

        $form->addSubmit('submit', 'Registrovat');

        $form->onValidate[] = [$this, 'validateForm'];
        $form->onSuccess[] = [$this, 'completeForm'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function validateForm(Form $form, ArrayHash $values): void
    {
        if (!$form->isValid())
        {
            return;
        }

        if (empty($values['email']) || empty($values['username']) || empty($values['password']) || empty($values['passwordCheck']))
        {
            $form->addError('Nebyly vyplněny všechny hodnoty.');
        }

        if($this->userFacade->getByEmail($values['email']) !== null)
        {
            $form->addError('Uživatel s tímto emailem již existuje.');
        }

        if($this->userFacade->getByUsername($values['username']) !== null)
        {
            $form->addError('Uživatel s tímto jménem již existuje.');
        }

        if($form->values['password'] !== $values['passwordCheck'])
        {
            $form->addError('Hesla se neshodují.');
        }
    }

    /**
     * @throws AbortException
     */
    public function completeForm(Form $form, ArrayHash $values): void
    {
        try {
            $this->userFacade->register($values);
            $this->presenter->redirect('Sign:upVerify');
        } catch (AbortException $exception) {
            throw $exception;
        } catch (\Exception|\Throwable $exception)
        {
            Debugger::log($exception, 'exception');
            $form->addError('Něco se nepovedlo.');
        }
    }
}