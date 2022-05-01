<?php

namespace App\Security;

use Contributte\MenuControl\IMenuItem;
use Contributte\MenuControl\Security\IAuthorizator;
use Nette\Security\User;

class MenuAuthorizator implements IAuthorizator
{
    public function __construct(
        private User $user
    )
    {
    }

    public function isMenuItemAllowed(IMenuItem $item): bool
    {
        $action = $item->getAction();
        if ($action === null)
        {
            return true;
        }

        $actionParams = explode(':', $action);

        return $this->user->isAllowed(implode(':', [$actionParams[0], $actionParams[1]]), $actionParams[2]);
    }
}