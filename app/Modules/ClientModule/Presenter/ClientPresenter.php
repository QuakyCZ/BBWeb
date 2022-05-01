<?php

namespace App\Modules\ClientModule\Presenter;

use Contributte\MenuControl\UI\IMenuComponentFactory;
use Contributte\MenuControl\UI\MenuComponent;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;
use Nette\Application\UI\Presenter;
use Nette\Security\AuthenticationException;

abstract class ClientPresenter extends Presenter
{

    private IMenuComponentFactory $menuComponentFactory;

    protected $presenterNamesWithPublicAccess = ['Sign', 'Error', 'Error4xx', 'Client:Sign', 'Client:Error', 'Client:Error4xx'];

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
        $storage->setNamespace('Client');

        $latte = $this->template->getLatte();

        $set = new MacroSet($latte->getCompiler());

        $set->addMacro(
            'addCss',
            function (MacroNode $node, PhpWriter $writer) {
                $txt = '<link rel="stylesheet" type="text/css" href=%node.word>';
                return $writer->write("echo '".$txt."'");
            }
        );

        if(!in_array($this->name, $this->presenterNamesWithPublicAccess))
        {
            if (!$this->user->isLoggedIn())
            {
                $key = $this->storeRequest();
                $this->redirect(':Client:Sign:in', ['returnKey' => $key]);
            }
            else if (!$this->user->isAllowed($this->getName(), $this->action))
            {
                throw new AuthenticationException("Nemáte dostatečná oprávnění " . $this->getName() . ":".$this->action);
            }
        }

        parent::startup();
    }

    protected function createComponentMenu(): MenuComponent
    {
        return $this->menuComponentFactory->create('client');
    }
}