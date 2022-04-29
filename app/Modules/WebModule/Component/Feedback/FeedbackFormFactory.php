<?php

namespace App\Modules\WebModule\Component\Feedback;

use App\Facade\MailFacade;
use App\Repository\FeedbackRepository;
use App\Repository\ServerRepository;

class FeedbackFormFactory
{
    /**
     * @param ServerRepository $serverRepository
     * @param FeedbackRepository $feedbackRepository
     * @param MailFacade $mailFacade
     */
    public function __construct
    (
        private ServerRepository $serverRepository,
        private FeedbackRepository $feedbackRepository,
        private MailFacade $mailFacade
    )
    {
    }

    /**
     * @return FeedbackForm
     */
    public function create(): FeedbackForm
    {
        return new FeedbackForm($this->serverRepository, $this->feedbackRepository, $this->mailFacade);
    }
}