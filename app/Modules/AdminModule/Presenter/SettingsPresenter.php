<?php

namespace App\Modules\AdminModule\Presenter;

use App\Modules\AdminModule\Component\Settings\ISettingsFormFactory;
use App\Modules\AdminModule\Component\Settings\ISettingsGridFactory;
use App\Modules\AdminModule\Component\Settings\SettingsForm;
use App\Modules\AdminModule\Component\Settings\SettingsGrid;
use App\Repository\SettingsRepository;

class SettingsPresenter extends Base\BasePresenter
{

    private ?int $id = null;

    public function __construct
    (
        private ISettingsFormFactory $settingsFormFactory,
        private ISettingsGridFactory $settingsGridFactory,
    )
    {
        parent::__construct();
    }

    /**
     * @param int $id
     * @return void
     */
    public function actionEdit(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return SettingsForm
     */
    public function createComponentSettingsForm(): SettingsForm
    {
        return $this->settingsFormFactory->create($this->id);
    }

    /**
     * @return SettingsGrid
     */
    public function createComponentSettingsGrid(): SettingsGrid
    {
        return $this->settingsGridFactory->create();
    }
}