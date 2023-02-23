<?php

namespace App\Repository\Primary;

use App\Model\ContextLocator;
use Nette\Database\Explorer;

abstract class PrimaryRepository extends \App\Repository\BaseRepository
{
    public function __construct(ContextLocator $contextLocator)
    {
        parent::__construct($contextLocator->getPrimary());
    }
}
