<?php

declare(strict_types=1);

namespace App\Modules\WebModule\Presenter;

use App\Model\MinecraftAPI\MinecraftPing;
use App\Model\MinecraftAPI\MinecraftPingException;
use App\Modules\WebModule\Component\RecentArticlesListing\IRecentArticlesListingFactory;
use App\Modules\WebModule\Component\RecentArticlesListing\RecentArticlesListing;
use App\Modules\WebModule\Presenter\Base\BasePresenter;
use Tracy\Debugger;

class HomepagePresenter extends BasePresenter
{

    /**
     * Class constructor
     * @param IRecentArticlesListingFactory $recentArticlesListingFactory
     */
    public function __construct(
        private IRecentArticlesListingFactory $recentArticlesListingFactory
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
            $query = new MinecraftPing($serverIp, 25565, 200000, true);
            $result = $query->Query();
            return $result['players']['online'] ?: null;
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
        return $this->recentArticlesListingFactory->create();
    }
}
