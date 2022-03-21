<?php

declare(strict_types=1);

namespace App\Modules\WebModule\Presenter;

use Nette;


class HomepagePresenter extends Nette\Application\UI\Presenter
{
    private Nette\Security\Passwords $passwords;
    public function __construct(Nette\Security\Passwords $passwords)
    {
        parent::__construct();
        $this->passwords = $passwords;
    }

    public function actionDefault(): void
    {
        bdump($this->passwords->hash('test'));
    }
}
