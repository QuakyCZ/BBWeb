<?php declare(strict_types=1);

namespace App\Utils;

class MinecraftUtils
{
    public static function convertUuidFromBinToString(string $uuid): string
    {
        $hex = bin2hex($uuid);

        $uuidWithDashes = substr($hex, 0, 8);
        $uuidWithDashes .= '-';
        $uuidWithDashes .= substr($hex, 8, 4);
        $uuidWithDashes .= '-';
        $uuidWithDashes .= substr($hex, 12, 4);
        $uuidWithDashes .= '-';
        $uuidWithDashes .= substr($hex, 16, 4);
        $uuidWithDashes .= '-';
        $uuidWithDashes .= substr($hex, 20, 12);

        return $uuidWithDashes;
    }
}