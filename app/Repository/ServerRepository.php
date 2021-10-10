<?php

namespace App\Repository;

class ServerRepository extends BaseRepository {
    private const TABLE_NAME = "server";
    protected string $tableName = self::TABLE_NAME;

    public function getAll() {
        return $this->database->table($this->tableName)->fetchAll();
    }
}