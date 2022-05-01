<?php

namespace App\Modules\ApiModule\Model\User;

use App\Repository\Primary\UserConnectTokenRepository;

class UserConnectTokenFacade
{
    public function __construct
    (
        private UserConnectTokenRepository $userConnectTokenRepository
    )
    {
    }
}