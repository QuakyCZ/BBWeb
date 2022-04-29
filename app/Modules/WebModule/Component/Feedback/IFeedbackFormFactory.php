<?php

namespace App\Modules\WebModule\Component\Feedback;

interface IFeedbackFormFactory
{
    /**
     * @return FeedbackForm
     */
    public function create(): FeedbackForm;
}