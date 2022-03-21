<?php

namespace App\Modules\AdminModule\Component\SignInForm;

interface ISignInFormFactory
{
    public function create(): SignInForm;
}