<?php

namespace App\Modules\ClientModule\Presenter;

use App\Modules\ClientModule\Component\Connection\Discord\DiscordConnection;
use App\Modules\ClientModule\Component\Connection\Discord\IDiscordConnectionFactory;
use App\Modules\ClientModule\Component\Connection\Minecraft\IMinecraftConnectionFactory;
use App\Modules\ClientModule\Component\Connection\Minecraft\MinecraftConnection;

class ConnectionsPresenter extends ClientPresenter
{

    public function __construct
    (
        private IMinecraftConnectionFactory    $minecraftConnectionFactory,
        private IDiscordConnectionFactory      $discordConnectionFactory,
    )
    {
        parent::__construct();
    }

    /**
     * @return MinecraftConnection
     */
    public function createComponentMinecraftConnection(): MinecraftConnection
    {
        return $this->minecraftConnectionFactory->create($this->getUser()->getId());
    }

    /**
     * @return DiscordConnection
     */
    public function createComponentDiscordConnection(): DiscordConnection
    {
        return $this->discordConnectionFactory->create($this->getUser()->getId());
    }
}