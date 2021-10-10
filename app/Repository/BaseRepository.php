<?php

namespace App\Repository;


use Nette\Database\Context;

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
}