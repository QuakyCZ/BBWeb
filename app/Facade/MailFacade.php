<?php

namespace App\Facade;

use Latte\Engine;
use Nette\Mail\Mailer;
use Nette\Mail\Message;

class MailFacade
{
    public const TEMPLATE_FEEDBACK_MAIL_RECIPIENT = __DIR__ . '/../Mail/FeedbackMailRecipient.latte';
    public const TEMPLATE_FEEDBACK_MAIL_SENDER = __DIR__ . '/../Mail/FeedbackMailSender.latte';
    public const TEMPLATE_RESET_PASSWORD = __DIR__ . '/../Mail/ResetPasswordMail.latte';
    public const TEMPLATE_VERIFICATION_EMAIL = __DIR__ . '/../Mail/VerificationMail.latte';

    /**
     * @param Mailer $mailer
     */
    public function __construct(
        private Mailer $mailer
    ) {
    }

    /**
     * @param string $email
     * @param string $subject
     * @param string $lattePath
     * @param array $params
     * @return void
     */
    public function sendMail(string $email, string $subject, string $lattePath, array $params): void
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

    /**
     * @param string $email
     * @param string $resetUrl
     * @return void
     */
    public function sendResetPasswordMail(string $email, string $resetUrl): void
    {
        $this->sendMail($email, 'Požadavek na změnu hesla', self::TEMPLATE_RESET_PASSWORD, [
            'url' => $resetUrl
        ]);
    }
}
