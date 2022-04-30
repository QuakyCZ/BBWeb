<?php

namespace App\Repository\DungeonEscape;

use App\Model\ContextLocator;
use App\Repository\BaseRepository;

class PlayerRepository extends BaseRepository
{

    public const TABLE_NAME = 'player';

    protected string $tableName = self::TABLE_NAME;

    public function __construct(ContextLocator $locator)
    {
        parent::__construct($locator->getDungeonEscape());
    }


}