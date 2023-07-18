<?php

namespace App\Model;

use Nette\Database\Explorer;

interface ContextLocator
{
    public function getPrimary(): Explorer;

    public function getDungeonEscape(): Explorer;

    public function getRewards(): Explorer;

    public function getLuckPerms(): Explorer;
}
