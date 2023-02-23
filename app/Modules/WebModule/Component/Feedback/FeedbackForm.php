<?php

namespace App\Modules\WebModule\Component\Feedback;

use App\Component\BaseComponent;
use App\Facade\MailFacade;
use App\Repository\Primary\FeedbackRepository;
use App\Repository\Primary\ServerRepository;
use Nette\Application\AbortException;
use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\Form;
use Nette\Database\Explorer;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;

class FeedbackForm extends BaseComponent
{
    /**
     * @param ServerRepository $serverRepository
     * @param FeedbackRepository $feedbackRepository
     * @param MailFacade $mailFacade
     * @param array $recaptchaConfig
     */
    public function __construct(
        private ServerRepository $serverRepository,
        private FeedbackRepository $feedbackRepository,
        private MailFacade $mailFacade,
    ) {
    }

    /**
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $form = new Form();
        $form->addText('nick', 'Nick')
            ->setRequired('%label je povinný údaj.');

        $form->addEmail('email', 'Email')
            ->setRequired('%label je povinný údaj.');

        $form->addSelect('server_id', 'Služba', $this->serverRepository->fetchItemsForChoiceControl())
            ->setRequired('%label je povinný údaj.')
            ->setPrompt('Služba');

        $form->addTextArea('description', 'Popis')
            ->setRequired('%label je povinný údaj.');

        $form->addReCaptcha('recaptcha', 'reCaptcha', )
            ->setRequired('Potvrďte, prosím, že nejste robot.');

        $form->addSubmit('save', 'Odeslat');
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
    public function saveForm(Form $form, ArrayHash $values): void
    {
        $xUrl = $form->getHttpData($form::DATA_TEXT, 'x_url');

        if (empty($xUrl) || $xUrl !== "nospam") {
            throw new ForbiddenRequestException();
        }

        $server = $this->serverRepository->findBy(['id' => $values['server_id']])->fetch();
        if ($server === null) {
            $form->addError('Neplatný server.');
            return;
        }

        $repo = $this->feedbackRepository;

        try {
            $repo->runInTransaction(
                function () use ($values, $server, $repo) {
                    $data = (array)$values;
                    $row = $repo->save($data);

                    $data['server'] = $server['name'];
                    $data['feedbackId'] = $row['id'];

                    $this->mailFacade->sendMail(
                        $data['email'],
                        'Zpětná vazba',
                        __DIR__.'/../../../../Mail/FeedbackMailSender.latte',
                        $data
                    );

                    $data['emailSender'] = $data['email'];

                    $this->mailFacade->sendMail(
                        'info@beastblock.cz',
                        'Zpětná vazba #' . $row['id'],
                        __DIR__.'/../../../../Mail/FeedbackMailRecipient.latte',
                        $data
                    );
                }
            );
            $this->presenter->redirect('Feedback:', ['sent' => true]);
        } catch (AbortException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            Debugger::log($exception, 'feedback_form');
            $form->addError('Nastala chyba při ukládání. Opakujte akci později.');
        }
    }
}
