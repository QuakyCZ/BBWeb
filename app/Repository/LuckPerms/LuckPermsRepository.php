<?php

namespace App\Repository\LuckPerms;

use App\Model\ContextLocator;
use App\Repository\BaseRepository;
use Nette\Database\Table\Selection;
use Nette\NotImplementedException;

abstract class LuckPermsRepository extends BaseRepository
{
    public function __construct(ContextLocator $contextLocator)
    {
        parent::__construct($contextLocator->getLuckPerms());
    }

    public function findAll(bool $withDeleted = false): Selection
    {
        return parent::findAll(true);
    }

    public function findBy(array $conditions, bool $withDeleted = true): Selection
    {
        return parent::findBy($conditions, true); // TODO: Change the autogenerated stub
    }

    public function setNotDeletedNull(int $id): int
    {
        throw new NotImplementedException('LuckPerms does not have NOT_DELETED column');
    }
}