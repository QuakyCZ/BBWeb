<?php

namespace App\Modules\ApiModule\v1\Enum;

class EErrorScopeType extends \MabeEnum\Enum
{
    public const API_ERROR = 'api';
    public const LOGICAL_ERROR = 'logical';
    public const SERVER_ERROR = 'server';
}