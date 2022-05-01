<?php

namespace App\Modules\ClientModule\Component\MinecraftConnect;

use App\Modules\ApiModule\Model\User\UserFacade;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;

class MinecraftConnectForm extends \App\Component\BaseComponent
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
        $form->addText('token', 'Token')
            ->setRequired('Toto je povinné pole.')
            ->setHtmlAttribute('placeholder', 'Token z MC');
        $form->addSubmit('submit', 'Propojit');
        $form->onSuccess[] = [$this, 'connect'];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function connect(Form $form, ArrayHash $values): void
    {
        try
        {
            $this->userFacade->connectMinecraft($this->presenter->getUser()->getId(), $values['token']);
            $this->presenter->flashMessage('Účet byl propojen.', 'success');
            $this->presenter->redirect('this');
        }
        catch (BadRequestException $exception)
        {
            $form->addError($exception->getMessage());
        }
        catch (AbortException $exception)
        {
            throw $exception;
        }
        catch (\Exception|\Throwable $exception)
        {
            Debugger::log($exception, 'minecraft-connect-form');
            $form->addError('Při zpracování požadavku nastala chyba. Opakujte akci později.');
        }
    }
}