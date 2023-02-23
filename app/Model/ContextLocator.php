<?php

namespace App\Model;

use Nette\Database\Explorer;

interface ContextLocator
{
    public function getPrimary(): Explorer;

    public function getDungeonEscape(): Explorer;
}
