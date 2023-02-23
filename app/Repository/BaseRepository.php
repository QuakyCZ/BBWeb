<?php

namespace App\Repository;

use Nette\Database\Context;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Traversable;

use function _PHPStan_76800bfb5\React\Promise\reduce;

abstract class BaseRepository
{
    public Explorer $database;

    protected string $tableName;

    /**
     * @param Context $database
     */
    public function __construct(Explorer $database)
    {
        $this->database = $database;
    }

    /**
     * @param array $data
     * @return array|bool|ActiveRow|int|Selection
     */
    public function save(array $data): array|bool|ActiveRow|int|Selection
    {
        if (isset($data['id'])) {
            $id = $data['id'];
            unset($data['id']);
            $row = $this->findBy(['id' => $id])->fetch();
            if ($row === null) {
                return false;
            }
            $row->update($data);
            return $row;
        }
        return $this->database->table($this->tableName)->insert($data);
    }

    /**
     * @param mixed $primary
     * @param bool $withDeleted
     * @return ActiveRow|null
     */
    public function findRow(mixed $primary, bool $withDeleted = false): ?ActiveRow
    {
        $selection = $this->database->table($this->tableName)
            ->wherePrimary($primary);

        if (!$withDeleted) {
            $selection->where('not_deleted', 1);
        }

        return $selection->fetch();
    }

    public function findBy(array $conditions, bool $withDeleted = false): Selection
    {
        if ($withDeleted === false) {
            $conditions[$this->tableName . '.not_deleted'] = 1;
        }

        $selection = $this->database->table($this->tableName);
        foreach ($conditions as $condition => $value) {
            $selection->where($condition, $value);
        }

        return $selection;
    }

    public function findAll(bool $withDeleted = false): Selection
    {
        if ($withDeleted) {
            return $this->database->table($this->tableName);
        }

        return $this->database->table($this->tableName)->where($this->tableName.'.not_deleted = 1');
    }

    public function getRow(int $id): ?ActiveRow
    {
        return $this->findBy([
            'id' => $id
        ])->fetch();
    }

    /**
     * @param int $id
     * @return int
     */
    public function setNotDeletedNull(int $id): int
    {
        return $this->findBy(['id' => $id])->update(['not_deleted' => null]);
    }

    /**
     * @param callable $callback
     * @return mixed
     */
    public function runInTransaction(callable $callback): mixed
    {
        return $this->database->transaction($callback);
    }

    /**
     * @param int $page
     * @param int $maxPerPage
     * @param int $pageCount
     * @return Selection
     */
    public function page(int $page, int $maxPerPage, int & $pageCount): Selection
    {
        return $this->findAll()
            ->page($page, $maxPerPage, $pageCount);
    }
}
