<?php

namespace App\Modules\ClientModule\Component\Connection;

use App\Component\BaseComponent;
use App\Enum\EFlashMessageType;
use App\Modules\ApiModule\Model\User\Connector\BaseUserConnectFacade;
use App\Modules\ApiModule\Model\User\Connector\UserConnectFacadeFactory;
use App\Repository\Primary\UserMinecraftAccountRepository;
use Nette\Application\AbortException;
use Nette\Database\Table\ActiveRow;
use Tracy\Debugger;
use Tracy\ILogger;

abstract class BaseConnection extends BaseComponent
{

    protected ?ActiveRow $account = null;

    public function __construct
    (
        protected ?int $userId,
        protected BaseUserConnectFacade $userConnectFacade
    )
    {
    }

    public function render(): void
    {
        $this->template->isConnected = $this->getAccount() !== null;
        parent::render();
    }

    /**
     * @return ActiveRow|null
     */
    protected function getAccount(): ?ActiveRow
    {
        if ($this->account === null)
        {
            $this->account = $this->userConnectFacade->getAccount($this->userId);
        }

        return $this->account;
    }

    /**
     * @param int $userId
     * @return void
     * @throws AbortException
     */
    public function handleDisconnect(): void
    {
        try
        {
            $message = new \stdClass();
            if ($this->userConnectFacade->disconnect($this->userId))
            {
                $message->type = EFlashMessageType::MODAL_WARNING;
                $message->title = 'Chyba';
                $message->message = 'Tento účet nemáte propojen.';
            }
            else
            {
                $message->type = EFlashMessageType::INFO;
                $message->message = 'Účet byl odpojen.';
            }
            $this->flashMessage($message);
        }
        catch (\Exception $exception)
        {
            Debugger::log($exception, ILogger::EXCEPTION);
            $message = new \stdClass();
            $message->type = EFlashMessageType::MODAL_WARNING;
            $message->title = 'Chyba';
            $message->message = 'Nastala neznámá chyba.';
            $this->flashMessage($message);
        }

        if ($this->presenter->isAjax())
        {
            $this->redrawControl('flashes');
            $this->redrawControl('connection');
        }
        else
        {
            $this->presenter->redirect('default');
        }
    }

    /**
     * @return void
     */
    abstract public function handleGenerateToken(): void;
}