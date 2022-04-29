<?php

namespace App\Modules\WebModule\Component\Feedback;

use App\Component\BaseComponent;
use App\Repository\FeedbackRepository;
use App\Repository\ServerRepository;
use Latte\Engine;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Database\Explorer;
use Nette\Mail\Mailer;
use Nette\Mail\Message;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;

class FeedbackForm extends BaseComponent
{
    /**
     * @param ServerRepository $serverRepository
     * @param FeedbackRepository $feedbackRepository
     * @param Mailer $mailer
     */
    public function __construct(
        private ServerRepository $serverRepository,
        private FeedbackRepository $feedbackRepository,
        private Mailer $mailer,
    )
    {
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

        $form->addSelect('server_id', 'Server', $this->serverRepository->fetchItemsForChoiceControl())
            ->setRequired('%label je povinný údaj.');

        $form->addTextArea('description', 'Popis')
            ->setRequired('%label je povinný údaj.');

        $form->addSubmit('save', 'Odeslat');
        $form->onSuccess[] = [$this, 'saveForm'];
        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @return void
     * @throws AbortException
     */
    public function saveForm(Form $form, ArrayHash $values): void
    {
        $server = $this->serverRepository->findBy(['id' => $values['server_id']])->fetch();
        if ($server === null)
        {
            $form->addError('Neplatný server.');
            return;
        }

        $repo = $this->feedbackRepository;

        try
        {
            $repo->runInTransaction(
                function(Explorer $database) use ($values, $server, $repo) {
                    $data = (array)$values;
                    $row = $database->table(FeedbackRepository::TABLE_NAME)->insert($data);

                    $data['server'] = $server['name'];
                    $data['feedbackId'] = $row['id'];

                    $latte = new Engine();
                    $body = $latte->renderToString(__DIR__.'/../../../../Mail/FeedbackMailSender.latte', $data);

                    $message = new Message();
                    $message->setFrom('noreply@beastblock.cz', 'BeastBlock.cz');
                    $message->addReplyTo('info@beastblock.cz', 'BeastBlock.cz');
                    $message->addTo($values['email']);
                    $message->setSubject('Zpětná vazba.');
                    $message->setHtmlBody($body);
                    $this->mailer->send($message);
                });
            $this->presenter->redirect('Feedback:', ['sent' => true]);
        }
        catch (AbortException $exception)
        {
            throw $exception;
        }
        catch (\Exception $exception)
        {
            Debugger::log($exception, 'feedback_form');
            $form->addError('Nastala chyba při ukládání. Opakujte akci později.');
        }
    }
}