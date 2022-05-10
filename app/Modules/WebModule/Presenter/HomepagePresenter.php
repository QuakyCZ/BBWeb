<?php

declare(strict_types=1);

namespace App\Modules\WebModule\Presenter;

use App\Modules\WebModule\Presenter\Base\BasePresenter;

class HomepagePresenter extends BasePresenter
{

    /**
     * @return void
     */
    public function actionDefault(): void
    {
        $this->template->serverIp = $this->settingsRepository->getSettingValue('server-ip');
        $this->template->serverVersion = $this->settingsRepository->getSettingValue('server-version');
        $this->template->facebookUrl = $this->settingsRepository->getSettingValue('facebook_url');
        $this->template->instagramUrl = $this->settingsRepository->getSettingValue('instagram_url');
        $this->template->discordUrl = $this->settingsRepository->getSettingValue('discord_url');
    }
}
