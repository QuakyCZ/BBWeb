<?php

namespace App\Repository\Primary;

use Nette\Database\Table\ActiveRow;

class SettingsRepository extends PrimaryRepository
{
    public const TABLE_NAME = 'settings';

    public const COLUMN_ID = 'id';

    public const COLUMN_NAME = 'name';

    public const COLUMN_CONTENT = 'content';

    public const COLUMN_NULLABLE = 'nullable';

    protected string $tableName = self::TABLE_NAME;

    /**
     * @param string $name
     * @return string|null
     */
    public function getSettingValue(string $name): ?string
    {
        return $this->getByName($name)[self::COLUMN_CONTENT] ?? null;
    }

    /**
     * @param string $name
     * @return ActiveRow|null
     */
    public function getByName(string $name): ?ActiveRow
    {
        return $this->findBy([
            self::COLUMN_NAME => $name
        ], true)->fetch();
    }

    /**
     * @param int $id
     * @return ActiveRow|null
     */
    public function get(int $id): ?ActiveRow
    {
        return $this->findBy([self::COLUMN_ID => $id], true)->fetch();
    }
}
