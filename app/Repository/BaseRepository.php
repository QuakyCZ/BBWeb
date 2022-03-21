<?php

namespace App\Repository;


use Nette\Database\Context;
use Nette\Database\Table\Selection;
use function _PHPStan_76800bfb5\React\Promise\reduce;

class BaseRepository
{

    public Context $database;

    protected string $tableName;

    /**
     * @param Context $database
     */
    public function __construct(Context $database)
    {
        $this->database = $database;
    }

    public function save(array $data) {
        return $this->database->table($this->tableName)->insert($data);
    }

    public function findBy(array $conditions, bool $withDeleted = false): Selection {
        if ($withDeleted == false) {
            $conditions['not_deleted'] = 1;
        }

        $selection = $this->database->table($this->tableName);
        foreach ($conditions as $condition => $value) {
            $selection->where($condition, $value);
        }

        return $selection;
    }

    public function findAll(bool $withDeleted = false): Selection {
        if ($withDeleted)
            return $this->database->table($this->tableName);

        return $this->database->table($this->tableName)->where('not_deleted = 1');
    }

    /**
     * @param int $id
     * @return int
     */
    public function setNotDeletedNull(int $id): int {
        return $this->findBy(['id' => $id])->update(['not_deleted' => null]);
    }
}