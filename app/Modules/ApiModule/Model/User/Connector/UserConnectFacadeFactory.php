<?php

namespace App\Modules\ApiModule\Model\User\Connector;

use App\Enum\EConnectTokenType;

final class UserConnectFacadeFactory
{
    /**
     * @param MinecraftUserConnectFacade $minecraftUserConnectFacade
     * @param DiscordUserConnectFacade $discordUserConnectFacade
     */
    public function __construct(
        private MinecraftUserConnectFacade $minecraftUserConnectFacade,
        private DiscordUserConnectFacade $discordUserConnectFacade
    ) {
    }

    /**
     * @param string $type
     * @return BaseUserConnectFacade|null
     */
    public function getInstanceOf(string $type): ?BaseUserConnectFacade
    {
        switch ($type) {
            default:
                return null;
            case EConnectTokenType::MINECRAFT:
                return $this->minecraftUserConnectFacade;
            case EConnectTokenType::DISCORD:
                return $this->discordUserConnectFacade;
        }
    }
}
