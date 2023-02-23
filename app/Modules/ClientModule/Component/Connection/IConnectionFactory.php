<?php

namespace App\Modules\ClientModule\Component\Connection;

interface IConnectionFactory
{
    /**
     * @param int|null $userId
     * @return BaseConnection
     */
    public function create(?int $userId): BaseConnection;
}
