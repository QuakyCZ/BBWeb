<?php

namespace App\Modules\ApiModule\Model\Logger;

use Tomaj\NetteApi\Logger\ApiLoggerInterface;
use Tracy\Debugger;

class ApiLogger implements ApiLoggerInterface
{

    public function log(int $responseCode, string $requestMethod, string $requestHeader, string $requestUri, string $requestIp, string $requestAgent, int $responseTime): bool
    {
        Debugger::log($requestIp . ': Code: ' . $responseCode . ' ' . $requestMethod . ' ' . $requestHeader . ' ' . $requestUri . ' ' . $responseTime, 'api-log');
        return true;
    }
}