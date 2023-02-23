<?php

namespace App\Utils;

class MinecraftUtils
{
    /**
     * @param string $uuid
     * @return string
     */
    public static function uuid2hex(string $uuid): string
    {
        return str_replace('-', '', mb_strtolower($uuid));
    }

    /**
     * @param string $uuid
     * @return false|string converted uuid to bin or false if error
     */
    public static function uuid2bin(string $uuid): false|string
    {
        return @hex2bin(self::uuid2hex($uuid));
    }

    /**
     * @param string $bin
     * @return false|string converted bin to uuid or false if error
     */
    public static function bin2uuid(string $bin): false|string
    {
        $hex = @bin2hex($bin);
        if (!$hex) {
            return false;
        }
        return self::hex2uuid($hex);
    }

    /**
     * Converts hex uuid to minecraft formatted uuid with dashes.
     * @param string $uuidHex
     * @return string
     */
    public static function hex2uuid(string $uuidHex): string
    {
        $converted = substr_replace($uuidHex, '-', 23, 0);
        $converted = substr_replace($converted, '-', 18, 0);
        $converted = substr_replace($converted, '-', 13, 0);
        return substr_replace($converted, '-', 8, 0);
    }
}
