<?php

namespace App\Modules\ClientModule\Presenter;

use App\Enum\EConnectTokenType;
use App\Enum\EFlashMessageType;
use App\Modules\ApiModule\Model\User\Connector\UserConnectFacadeFactory;
use App\Modules\ApiModule\Model\User\UserFacade;
use App\Modules\ClientModule\Component\MinecraftConnect\IMinecraftConnectFormFactory;
use App\Modules\ClientModule\Component\MinecraftConnect\MinecraftConnectForm;
use App\Repository\Primary\UserDiscordAccountRepository;
use App\Repository\Primary\UserMinecraftAccountRepository;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Tracy\Debugger;
use Tracy\ILogger;

class ConnectionsPresenter extends ClientPresenter
{

    public function __construct
    (
        private UserFacade $userFacade,
        private UserConnectFacadeFactory $userConnectFacadeFactory,
        private UserMinecraftAccountRepository $userMinecraftAccountRepository,
        private UserDiscordAccountRepository $userDiscordAccountRepository,
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

        $discordConnection = $this->userDiscordAccountRepository->getAccountByUserId($this->getUser()->getId());
        $discordConnected = $discordConnection !== null;

        $this->template->isDiscordConnected = $discordConnected;
    }

    /**
     * @throws BadRequestException
     * @throws AbortException
     */
    public function actionDisconnect(string $type): void
    {
        try
        {
            $connector = $this->userConnectFacadeFactory->getInstanceOf($type);
            $connector->disconnect($this->user->getId());
            $this->flashMessage('Účet byl odpojen.', EFlashMessageType::INFO);
        }
        catch (BadRequestException $exception)
        {
            $this->flashMessage($exception->getMessage(), EFlashMessageType::WARNING);
            throw $exception;
        }
        catch (\PDOException $exception)
        {
            Debugger::log($exception, 'connections', EFlashMessageType::ERROR);
            $this->flashMessage('Při zpracování požadavku nastala chyba.');
        }

        $this->redirect('Connections:default');

    }

    /**
     * @throws AbortException
     */
    public function handleGenerateToken(string $type): void
    {
        if (!$this->user->isLoggedIn())
        {
            $this->sendJson([
                'status' => 'error',
                'message' => 'Neplatné přihlášení.'
            ]);
        }

        $message = new \stdClass();

        try
        {
            $token = $this->userFacade->createToken($this->getUser()->getId(), $type);
            $message->type = EFlashMessageType::MODAL_INFO;
            $message->title = 'Propojení ' . ucfirst($type);
            $message->message = 'Pro dokončení propojení zadejte na ' . ucfirst($type) .' serveru příkaz: <br><br> <code>/connectweb ' . $token . '</code>';


            $this->template->tokenGenerated = true;
            $this->template->token = $token;
        }
        catch (\Exception $exception)
        {
            Debugger::log($exception, ILogger::EXCEPTION);

            $message->type = EFlashMessageType::MODAL_WARNING;
            $message->title = 'Chyba';
            $message->message = 'Omlouváme se, při zpracování požadavku nastala chyba.';
        }

        $this->flashMessage($message,EFlashMessageType::MODAL_INFO);

        if ($this->isAjax())
        {
            $this->redrawControl('flashes');
        }
        else
        {
            $this->redirect('default');
        }


    }
}