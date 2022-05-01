<?php

namespace App\Modules\AdminModule\Component\SignUpForm;

interface ISignUpFormFactory
{
    /**
     * @return SignUpForm
     */
    public function create(): SignUpForm;
}