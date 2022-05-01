<?php

namespace App\Enum;

class EConnectTokenType
{
    public const DISCORD = 'discord';
    public const MINECRAFT = 'minecraft';

    public static array $array = [
        self::DISCORD,
        self::MINECRAFT
    ];
}