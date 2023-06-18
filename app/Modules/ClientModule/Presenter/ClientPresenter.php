<?php

namespace App\Modules\ClientModule\Presenter;

use App\Modules\ApiModule\Model\User\UserFacade;
use App\Repository\Primary\SettingsRepository;
use App\Repository\Primary\UserMinecraftAccountRepository;
use App\Repository\Primary\UserRepository;
use Contributte\MenuControl\UI\IMenuComponentFactory;
use Contributte\MenuControl\UI\MenuComponent;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Presenter;
use Nette\Security\AuthenticationException;
use Tracy\Debugger;

abstract class ClientPresenter extends Presenter
{
    private IMenuComponentFactory $menuComponentFactory;
    private UserRepository $userRepository;
    private UserMinecraftAccountRepository $userMinecraftAccountRepository;
    protected SettingsRepository $settingsRepository;

    protected $presenterNamesWithPublicAccess = ['Sign', 'Error', 'Error4xx', 'Client:Sign', 'Client:Error', 'Client:Error4xx'];

    /**
     * @param IMenuComponentFactory $menuComponentFactory
     * @param UserRepository $userRepository
     * @param UserMinecraftAccountRepository $userMinecraftAccountRepository
     * @param SettingsRepository $settingsRepository
     * @return void
     */
    public function injectBasePresenter(
        IMenuComponentFactory $menuComponentFactory,
        UserRepository $userRepository,
        UserMinecraftAccountRepository $userMinecraftAccountRepository,
        SettingsRepository $settingsRepository
    ): void {
        $this->menuComponentFactory = $menuComponentFactory;
        $this->userRepository = $userRepository;
        $this->userMinecraftAccountRepository = $userMinecraftAccountRepository;
        $this->settingsRepository = $settingsRepository;
    }

    /**
     * @throws AuthenticationException
     * @throws \Nette\Application\AbortException
     * @throws BadRequestException
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

        if (!in_array($this->name, $this->presenterNamesWithPublicAccess)) {
            $user = $this->getUser();
            $key = $this->storeRequest();
            if (!$user->isLoggedIn()) {
                $this->redirect(':Client:Sign:in', ['returnKey' => $key]);
            } elseif (!$this->userRepository->isUserActive($user->getId())) {
                $this->getUser()->logout(true);
                $this->redirect(':Client:Sign:in', ['returnKey' => $key]);
            } elseif (!$user->isAllowed($this->getName(), $this->getAction())) {
                throw new BadRequestException("Nemáte dostatečná oprávnění " . $this->getName() . ":".$this->getAction(), 403);
            }
        }

        parent::startup();
    }

    protected function beforeRender()
    {
        $alertEnabled = $this->settingsRepository->getByName('alert-enabled')['content'];
        $this->template->alertEnabled = $alertEnabled;

        if ($alertEnabled) {
            $alert = new \stdClass();
            $alert->icon = $this->settingsRepository->getByName('alert-icon')['content'];
            $alert->message = $this->settingsRepository->getByName('alert-message')['content'];
            $alert->backgroundColor = $this->settingsRepository->getByName('alert-bg-color')['content'];
            $alert->fontColor = $this->settingsRepository->getByName('alert-font-color')['content'];
            $alert->iconBackgroundColor = $this->settingsRepository->getByName('alert-icon-background-color')['content'];
            $this->template->alert = $alert;
        }

        if ($this->getUser()->isLoggedIn()) {
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
