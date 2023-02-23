<?php

namespace App\Modules\AdminModule\Component\ResetPasswordForm;

interface IResetPasswordFormFactory
{
    /**
     * @return ResetPasswordForm
     */
    public function create(): ResetPasswordForm;
}
