<?php

namespace App\Model;

use Nette\Database\Context;

interface ContextLocator
{
    public function getPrimary(): Context;

    public function getDungeonEscape(): Context;
}