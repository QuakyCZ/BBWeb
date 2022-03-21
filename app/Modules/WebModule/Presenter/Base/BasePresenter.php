<?php

namespace App\Modules\WebModule\Presenter\Base;

use Contributte\Translation\Translator;
use Nette\Application\UI\Presenter;

class BasePresenter extends Presenter {
    /** @persistent */
    public $locale;

    /** @var Translator @inject */
    public $translator;

    protected function startup() {
        parent::startup();
        $this->template->setTranslator($this->translator);
    }

}