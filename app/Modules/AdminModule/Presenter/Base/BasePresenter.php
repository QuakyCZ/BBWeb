<?php

namespace App\Modules\AdminModule\Presenter\Base;

use Contributte\MenuControl\UI\IMenuComponentFactory;
use Contributte\MenuControl\UI\MenuComponent;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;
use Nette\Application\UI\Presenter;
use Nette\Security\AuthenticationException;

abstract class BasePresenter extends Presenter
{
    private IMenuComponentFactory $menuComponentFactory;

    protected $presenterNamesWithPublicAccess = ['Sign', 'Error', 'Error4xx', 'Admin:Sign', 'Admin:Error', 'Admin:Error4xx'];

    public function injectBasePresenter(IMenuComponentFactory $menuComponentFactory)
    {
        $this->menuComponentFactory = $menuComponentFactory;
    }

    /**
     * @throws AuthenticationException
     * @throws \Nette\Application\AbortException
     */
    protected function startup()
    {
        $storage = $this->getUser()->getStorage();
        $storage->setNamespace('Admin');

        $latte = $this->template->getLatte();

        $set = new MacroSet($latte->getCompiler());

        $set->addMacro(
            'addCss',
            function (MacroNode $node, PhpWriter $writer) {
                $txt = '<link rel="stylesheet" type="text/css" href=%node.word>';
                return $writer->write("echo '".$txt."'");
            }
        );

        if (!in_array($this->name, $this->presenterNamesWithPublicAccess)) {
            if (!$this->user->isLoggedIn()) {
                $key = $this->storeRequest();
                $this->redirect(':Admin:Sign:in', ['returnKey' => $key]);
            } elseif (!$this->user->isAllowed($this->getName(), $this->action)) {
                throw new AuthenticationException("Nemáte dostatečná oprávnění " . $this->getName() . ":".$this->action);
            }
        }

        parent::startup();
    }

    protected function createComponentMenu(): MenuComponent
    {
        return $this->menuComponentFactory->create('admin');
    }
}
