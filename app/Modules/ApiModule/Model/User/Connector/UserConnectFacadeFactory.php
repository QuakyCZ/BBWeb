<?php

namespace App\Modules\ApiModule\Model\User\Connector;

use App\Enum\EConnectTokenType;
use Nette\Application\BadRequestException;

final class UserConnectFacadeFactory
{
    /**
     * @param MinecraftUserConnectFacade $minecraftUserConnectFacade
     * @param DiscordUserConnectFacade $discordUserConnectFacade
     */
    public function __construct
    (
        private MinecraftUserConnectFacade $minecraftUserConnectFacade,
        private DiscordUserConnectFacade $discordUserConnectFacade
    )
    {
    }

    /**
     * @param string $type
     * @return BaseUserConnectFacade
     * @throws BadRequestException
     */
    public function getInstanceOf(string $type): BaseUserConnectFacade
    {
        switch ($type)
        {
            default:
                throw new BadRequestException('Neznámý typ propojení.');
            case EConnectTokenType::MINECRAFT:
                return $this->minecraftUserConnectFacade;
            case EConnectTokenType::DISCORD:
                return $this->discordUserConnectFacade;
        }
    }
}