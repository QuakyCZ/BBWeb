<?php

namespace App\Facade;

use Latte\Engine;
use Nette\Mail\Mailer;
use Nette\Mail\Message;

class MailFacade
{
    /**
     * @param Mailer $mailer
     */
    public function __construct
    (
        private Mailer $mailer
    )
    {
    }

    /**
     * @param string $email
     * @param string $subject
     * @param string $lattePath
     * @param array $params
     * @return void
     */
    public function sendMail(string $email, string $subject, string $lattePath, array $params):void
    {
        $params['email'] = $email;
        $params['subject'] = $subject;

        $latte = new Engine();
        $body = $latte->renderToString($lattePath, $params);
        $message = new Message();
        $message->setFrom('noreply@beastblock.cz', 'BeastBlock.cz');
        $message->addReplyTo('info@beastblock.cz', 'BeastBlock.cz');
        $message->addTo($email);
        $message->setSubject($subject);
        $message->setHtmlBody($body);
        $this->mailer->send($message);
    }
}