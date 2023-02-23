<?php

namespace App\Modules\ClientModule\Component\Connection\Minecraft;

use App\Modules\ClientModule\Component\Connection\BaseConnection;
use App\Modules\ClientModule\Component\Connection\IConnectionFactory;
use App\Modules\ClientModule\Component\Connection\Minecraft\MinecraftConnection;

interface IMinecraftConnectionFactory extends IConnectionFactory
{
    /**
     * @param int|null $userId
     * @return BaseConnection
     */
    public function create(?int $userId): MinecraftConnection;
}
