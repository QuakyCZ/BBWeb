<?php

namespace App\Modules\ClientModule\Presenter;

class DashboardPresenter extends ClientPresenter
{
    public function actionDefault() {
        $this->flashMessage('hello');
    }
}