<?php

namespace App\Modules\WebModule\Presenter;

use App\Modules\WebModule\Component\Feedback\FeedbackForm;
use App\Modules\WebModule\Component\Feedback\FeedbackFormFactory;

class FeedbackPresenter extends Base\BasePresenter
{
    /**
     * @param FeedbackFormFactory $feedbackFormFactory
     */
    public function __construct
    (
        private FeedbackFormFactory $feedbackFormFactory,
    )
    {
        parent::__construct();
    }

    /**
     * @param bool|null $sent
     * @return void
     */
    public function actionDefault(?bool $sent = false): void
    {
        $this->template->sent = $sent;
    }

    public function createComponentFeedbackForm(): FeedbackForm
    {
        return $this->feedbackFormFactory->create();
    }
}