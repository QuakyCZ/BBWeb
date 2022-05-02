<?php

namespace App\Modules\AdminModule\Component\User;

interface IUserFormFactory
{
    /**
     * @param int|null $id UserID
     * @return UserForm
     */
    public function create(?int $id = null): UserForm;
}