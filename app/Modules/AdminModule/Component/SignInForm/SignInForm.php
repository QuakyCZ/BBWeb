<?php

namespace App\Modules\AdminModule\Component\SignInForm;

use App\Component\BaseComponent;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Tracy\Debugger;

class SignInForm extends BaseComponent
{
    public function __construct(
        private ?string $defaultRoute = 'Default:',
        private ?string $returnKey = null,
    ) {
    }

    public function createComponentForm(): Form
    {
        $form = new Form();
        $form->addHidden('returnKey')
            ->setDefaultValue($this->returnKey);
        $form->addEmail('email', 'Email')
            ->setHtmlAttribute('placeholder', 'priklad@beastblock.cz')
            ->setRequired('%label je povinný údaj');
        $form->addPassword('password', 'Heslo')
            ->setHtmlAttribute('placeholder', '×××××××××××')
            ->setRequired('%label je povinný údaj');
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
        try {
            $this->presenter->user->login($values['email'], $values['password']);
            $this->presenter->flashMessage('Přihlášení proběhlo úspěšně');
            $returnKey = $values['returnKey'] ?? null;
            if ($returnKey !== null) {
                $this->presenter->restoreRequest($returnKey);
            }

            $this->presenter->redirect($this->defaultRoute);
        } catch (AbortException $exception) {
            throw $exception;
        } catch (AuthenticationException $e) {
            $form->addError($e->getMessage());
        } catch (\Exception $exception) {
            Debugger::log($exception, 'login');
            $form->addError('Nastala neznámá chyba.');
        }
    }
}
