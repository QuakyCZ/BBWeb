<?php

declare(strict_types=1);

namespace App\Modules\WebModule\Presenter;

use App\Modules\WebModule\Component\RecentArticlesListing\IRecentArticlesListingFactory;
use App\Modules\WebModule\Component\RecentArticlesListing\RecentArticlesListing;
use App\Modules\WebModule\Component\ServerListing\IServerListingFactory;
use App\Modules\WebModule\Component\ServerListing\ServerListing;
use App\Modules\WebModule\Presenter\Base\BasePresenter;
use Tracy\Debugger;
use xPaw\MinecraftPing;
use xPaw\MinecraftPingException;

class HomepagePresenter extends BasePresenter
{

    /**
     * Class constructor
     * @param IRecentArticlesListingFactory $recentArticlesGridFactory
     * @param IServerListingFactory $serverListingFactory
     */
    public function __construct(
        private IRecentArticlesListingFactory $recentArticlesGridFactory,
        private IServerListingFactory $serverListingFactory,
    )
    {
        parent::__construct();
    }

    /**
     * @return void
     */
    public function actionDefault(): void
    {
        $serverIp = $this->settingsRepository->getSettingValue('server-ip');
        $this->template->serverIp = $serverIp;
        $this->template->players = $this->fetchMinecraftPlayers($serverIp);
        $this->template->serverVersion = $this->settingsRepository->getSettingValue('server-version');
        $this->template->facebookUrl = $this->settingsRepository->getSettingValue('facebook_url');
        $this->template->instagramUrl = $this->settingsRepository->getSettingValue('instagram_url');
        $this->template->discordUrl = $this->settingsRepository->getSettingValue('discord_url');
    }

    /**
     * Get the number of players on the server
     * @param string $serverIp
     * @return int|null
     */
    private function fetchMinecraftPlayers(string $serverIp): ?int
    {
        $query = null;
        try
        {
            bdump($serverIp);
            $query = new MinecraftPing($serverIp, 25565, 200000, true);
            $result = $query->Query();
            return $result['players']['online'] ?? null;
        }
        catch (MinecraftPingException $exception)
        {
            Debugger::log($exception, 'minecraft');
        }
        finally
        {
            $query?->Close();
        }

        return null;
    }

    /**
     * Creates RecentArticlesListing component
     * @return RecentArticlesListing
     */
    public function createComponentRecentArticlesListing(): RecentArticlesListing
    {
        return $this->recentArticlesGridFactory->create();
    }

    /**
     * Creates ServerListing component
     * @return ServerListing
     */
    public function createComponentServerListing(): ServerListing {
        return $this->serverListingFactory->create();
    }
}
