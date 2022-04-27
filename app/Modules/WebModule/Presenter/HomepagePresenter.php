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
        $this->template->serverIp = $this->settingRepository->getSettingValue('server-ip');
        $this->template->serverVersion = $this->settingRepository->getSettingValue('server-version');
        $this->template->facebookUrl = $this->settingRepository->getSettingValue('facebook_url');
        $this->template->instagramUrl = $this->settingRepository->getSettingValue('instagram_url');
        $this->template->discordUrl = $this->settingRepository->getSettingValue('discord_url');
    }
}
