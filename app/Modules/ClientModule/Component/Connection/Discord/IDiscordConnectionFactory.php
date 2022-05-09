<?php

namespace App\Modules\ClientModule\Component\Connection\Discord;

use App\Modules\ClientModule\Component\Connection\IConnectionFactory;

interface IDiscordConnectionFactory extends IConnectionFactory
{
    /**
     * @param int|null $userId
     * @return DiscordConnection
     */
    public function create(?int $userId = null): DiscordConnection;
}