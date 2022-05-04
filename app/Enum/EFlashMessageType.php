<?php

namespace App\Enum;

class EFlashMessageType
{
    public const INFO = 'info';
    public const SUCCESS = 'success';
    public const WARNING = 'warning';
    public const ERROR = 'error';

    public const TYPE_TO_FA_ICON = [
        self::INFO => 'fa-solid fa-circle-info',
        self::SUCCESS => 'fa-solid fa-circle-check',
        self::WARNING => 'fa-solid fa-circle-exclamation',
        self::ERROR => 'fa-solid fa-bug'
    ];

    public const TYPE_TO_ICON_COLOR = [
        self::INFO => '#fff'
    ];
}