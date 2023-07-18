<?php

namespace App\Repository\LuckPerms;

class LuckPermsUserPermissionsRepository extends LuckPermsRepository
{
    public const TABLE_NAME = 'luckperms_user_permissions';

    public const COLUMN_ID = 'id';
    public const COLUMN_UUID = 'uuid';
    public const COLUMN_PERMISSION = 'permission';
    public const COLUMN_VALUE = 'value';
    public const COLUMN_SERVER = 'server';
    public const COLUMN_WORLD = 'world';
    public const COLUMN_EXPIRY = 'expiry';
    public const COLUMN_CONTEXTS = 'contexts';

    protected string $tableName = self::TABLE_NAME;

    public const PERMISSION_BEASTBLOCK_JOIN_SUBSERVER = 'beastblock.join.subserver';

    public const SERVER_GLOBAL = 'global';
    public const WORLD_GLOBAL = 'global';

    public const CONTEXTS_EMPTY = '{}';

    public function setSubserverPermission(string $uuid, bool $value): void
    {
        $row = $this->findBy([
            self::COLUMN_UUID => $uuid,
            self::COLUMN_PERMISSION => self::PERMISSION_BEASTBLOCK_JOIN_SUBSERVER,
        ])->fetch();

        $data = [
            self::COLUMN_UUID => $uuid,
            self::COLUMN_EXPIRY => $value === true ? strtotime('tomorrow midnight') : 0,
            self::COLUMN_VALUE => $value,
            self::COLUMN_SERVER => self::SERVER_GLOBAL,
            self::COLUMN_WORLD => self::WORLD_GLOBAL,
            self::COLUMN_PERMISSION => self::PERMISSION_BEASTBLOCK_JOIN_SUBSERVER,
            self::COLUMN_CONTEXTS => self::CONTEXTS_EMPTY,
        ];

        if ($row !== null) {
            $row->update($data);
        } else {
            $this->save($data);
        }
    }
}