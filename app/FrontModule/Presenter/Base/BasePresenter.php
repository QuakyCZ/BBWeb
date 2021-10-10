<?php

namespace App\FrontModule\Presenter\Base;

use Contributte\Translation\Translator;
use Nette\Application\UI\Presenter;

class BasePresenter extends Presenter {
    /** @persistent */
    public $locale;

    /** @var Translator @inject */
    public $translator;

}