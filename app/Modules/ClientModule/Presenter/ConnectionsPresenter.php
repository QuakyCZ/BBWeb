<?php

namespace App\Modules\ClientModule\Presenter;

use App\Enum\EConnectTokenType;
use App\Modules\ApiModule\Model\User\UserFacade;
use App\Modules\ClientModule\Component\MinecraftConnect\IMinecraftConnectFormFactory;
use App\Modules\ClientModule\Component\MinecraftConnect\MinecraftConnectForm;
use App\Repository\Primary\UserMinecraftAccountRepository;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Tracy\Debugger;

class ConnectionsPresenter extends ClientPresenter
{

    public function __construct
    (
        private UserFacade $userFacade,
        private UserMinecraftAccountRepository $userMinecraftAccountRepository,
        private IMinecraftConnectFormFactory $minecraftConnectFormFactory
    )
    {
        parent::__construct();
    }

    public function actionDefault(): void
    {
        $minecraftConnection = $this->userMinecraftAccountRepository->getAccountByUserId($this->getUser()->getId());
        $minecraftConnected = $minecraftConnection !== null;

        $this->template->isMinecraftConnected = $minecraftConnected;
        if ($minecraftConnected)
        {
            $this->template->minecraftNick = $minecraftConnection[UserMinecraftAccountRepository::COLUMN_NICK];
        }
    }

    /**
     * @throws BadRequestException
     * @throws AbortException
     */
    public function actionDisconnect(string $type): void
    {
        try
        {
            $this->userFacade->disconnect($this->getUser()->getId(), $type);
            $this->flashMessage('Účet byl odpojen.', 'warning');
        }
        catch (BadRequestException $exception)
        {
            $this->flashMessage($exception->getMessage());
            throw $exception;
        }
        catch (\PDOException $exception)
        {
            Debugger::log($exception, 'connections');
            $this->flashMessage('Při zpracování požadavku nastala chyba.');
        }

        $this->redirect('Connections:default');

    }

    public function createComponentMinecraftConnectForm(): MinecraftConnectForm
    {
        return $this->minecraftConnectFormFactory->create();
    }
}