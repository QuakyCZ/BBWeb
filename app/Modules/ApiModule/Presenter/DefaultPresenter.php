<?php

namespace App\Modules\ApiModule\Presenter;

use App\Modules\WebModule\Presenter\Base\BasePresenter;
use Nette\Application\AbortException;

class DefaultPresenter extends BasePresenter
{

    /**
     * @return void
     * @throws AbortException
     */
    public function actionDefault(): void
    {
        $this->sendJson(['info' => 'BeastBlock API v1.0']);
    }
}