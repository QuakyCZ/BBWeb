<?php

declare(strict_types=1);

namespace App\Modules\WebModule\Presenter;

use App\Modules\WebModule\Presenter\Base\BasePresenter;
use Nette\Security\Passwords;


class HomepagePresenter extends BasePresenter
{

    /**
     * @return void
     */
    public function actionDefault(): void
    {
        $this->template->serverIp = $this->settingRepository->getSettingValue('server-ip');
        $this->template->serverVersion = $this->settingRepository->getSettingValue('server-version');
    }
}
