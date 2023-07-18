<?php

namespace App\Modules\ClientModule\Component\Connection\Twitch;

interface ITwitchConnectionFactory
{
    /**
     * @param int|null $userId
     * @return TwitchConnection
     */
    public function create(?int $userId): TwitchConnection;
}