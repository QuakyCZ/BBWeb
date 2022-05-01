<?php

namespace App\Modules\AdminModule\Component\SignInForm;

interface ISignInFormFactory
{
    public function create(?string $defaultRoute = 'Default:', ?string $returnKey = null): SignInForm;
}