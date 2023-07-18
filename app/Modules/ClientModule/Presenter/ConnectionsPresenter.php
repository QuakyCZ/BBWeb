<?php

namespace App\Modules\ClientModule\Presenter;

use App\Enum\EFlashMessageType;
use App\Modules\ApiModule\Model\User\Connector\TwitchUserConnectFacade;
use App\Modules\ClientModule\Component\Connection\Discord\DiscordConnection;
use App\Modules\ClientModule\Component\Connection\Discord\IDiscordConnectionFactory;
use App\Modules\ClientModule\Component\Connection\Minecraft\IMinecraftConnectionFactory;
use App\Modules\ClientModule\Component\Connection\Minecraft\MinecraftConnection;
use App\Modules\ClientModule\Component\Connection\Twitch\ITwitchConnectionFactory;
use App\Modules\ClientModule\Component\Connection\Twitch\TwitchConnection;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;

class ConnectionsPresenter extends ClientPresenter
{
    /**
     * @param IMinecraftConnectionFactory $minecraftConnectionFactory
     * @param IDiscordConnectionFactory $discordConnectionFactory
     * @param ITwitchConnectionFactory $twitchConnectionFactory
     * @param TwitchUserConnectFacade $twitchUserConnectFacade
     */
    public function __construct(
        private readonly IMinecraftConnectionFactory $minecraftConnectionFactory,
        private readonly IDiscordConnectionFactory $discordConnectionFactory,
        private readonly ITwitchConnectionFactory $twitchConnectionFactory,
        private readonly TwitchUserConnectFacade $twitchUserConnectFacade,
    ) {
        parent::__construct();
    }

    /**
     * @param string $code
     * @return void
     * @throws AbortException
     */
    public function actionDefault(string $code = ''): void {
        if (!empty($code)) {
            $response = $this->twitchUserConnectFacade->connect($this->getUser()->getId(), [
                'code' => $code
            ]);

            if ($response->getCode() !== 200) {
                $this->flashMessage('Při propojování účtu nastala chyba', EFlashMessageType::ERROR);
            } else {
                $this->flashMessage('Účet byl úspěšně propojen', EFlashMessageType::SUCCESS);
            }

            $this->redirect('Connections:default', []);
        }
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

    public function createComponentTwitchConnection(): TwitchConnection
    {
        return $this->twitchConnectionFactory->create($this->getUser()->getId());
    }
}
