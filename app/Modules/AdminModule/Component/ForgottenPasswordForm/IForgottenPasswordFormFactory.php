<?php

namespace App\Modules\AdminModule\Component\ForgottenPasswordForm;

interface IForgottenPasswordFormFactory
{
    /**
     * @return ForgottenPasswordForm
     */
    public function create(): ForgottenPasswordForm;
}