<?php

namespace App\Modules\ClientModule\Presenter;

use App\Modules\ApiModule\Model\User\UserFacade;
use App\Repository\Primary\UserMinecraftAccountRepository;
use App\Repository\Primary\UserRepository;
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
    private UserRepository $userRepository;
    private UserMinecraftAccountRepository $userMinecraftAccountRepository;

    protected $presenterNamesWithPublicAccess = ['Sign', 'Error', 'Error4xx', 'Client:Sign', 'Client:Error', 'Client:Error4xx'];

    /**
     * @param IMenuComponentFactory $menuComponentFactory
     * @param UserRepository $userRepository
     * @param UserMinecraftAccountRepository $userMinecraftAccountRepository
     * @return void
     */
    public function injectBasePresenter (
        IMenuComponentFactory $menuComponentFactory,
        UserRepository $userRepository,
        UserMinecraftAccountRepository $userMinecraftAccountRepository
    ): void
    {
        $this->menuComponentFactory = $menuComponentFactory;
        $this->userRepository = $userRepository;
        $this->userMinecraftAccountRepository = $userMinecraftAccountRepository;
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
            $user = $this->getUser();
            $key = $this->storeRequest();
            if (!$user->isLoggedIn())
            {
                $this->redirect(':Client:Sign:in', ['returnKey' => $key]);
            }
            else if (!$this->userRepository->isUserActive($user->getId()))
            {
                $this->getUser()->logout(true);
                $this->redirect(':Client:Sign:in', ['returnKey' => $key]);
            }
            else if (!$user->isAllowed($this->getName(), $this->getAction()))
            {
                throw new AuthenticationException("Nemáte dostatečná oprávnění " . $this->getName() . ":".$this->getAction());
            }
        }

        parent::startup();
    }

    protected function beforeRender()
    {
        if ($this->getUser()->isLoggedIn())
        {
            $minecraft = $this->userMinecraftAccountRepository->getAccountByUserId($this->getUser()->getId());
            $this->template->minecraftNick = $minecraft !== null ? $minecraft[UserMinecraftAccountRepository::COLUMN_NICK] : $this->getUser()->getIdentity()->getData()['name'];
        }

        parent::beforeRender();
    }

    protected function createComponentMenu(): MenuComponent
    {
        return $this->menuComponentFactory->create('client');
    }
}