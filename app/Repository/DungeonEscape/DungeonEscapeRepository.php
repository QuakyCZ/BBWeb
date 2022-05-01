<?php

namespace App\Repository\DungeonEscape;

use App\Model\ContextLocator;
use App\Repository\BaseRepository;

abstract class DungeonEscapeRepository extends BaseRepository
{
    /**
     * @param ContextLocator $contextLocator
     */
    public function __construct(ContextLocator $contextLocator)
    {
        parent::__construct($contextLocator->getDungeonEscape());
    }
}