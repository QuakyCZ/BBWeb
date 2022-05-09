<?php

namespace App\Enum;

class EFlashMessageType
{
    public const INFO = 'info';
    public const SUCCESS = 'success';
    public const WARNING = 'warning';
    public const ERROR = 'error';
    public const MODAL_INFO = 'modal_info';
    public const MODAL_WARNING = 'modal_warning';

    public const TYPE_TO_FA_ICON = [
        self::INFO => 'fa-solid fa-circle-info',
        self::SUCCESS => 'fa-solid fa-circle-check',
        self::WARNING => 'fa-solid fa-circle-exclamation',
        self::ERROR => 'fa-solid fa-bug',
        self::MODAL_INFO => 'info',
        self::MODAL_WARNING => 'warning'
    ];

    public const TYPE_TO_ICON_COLOR = [
        self::INFO => 'cyan',
        self::SUCCESS => 'LawnGreen',
        self::WARNING => 'orange',
        self::ERROR => 'red',
        self::MODAL_INFO => 'cyan',
        self::MODAL_WARNING => 'orange'
    ];
}