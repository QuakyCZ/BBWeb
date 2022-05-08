<?php

namespace App\Modules\ClientModule\Component\Connection\Discord;

use App\Enum\EFlashMessageType;
use App\Modules\ApiModule\Model\User\Connector\DiscordUserConnectFacade;
use App\Modules\ClientModule\Component\Connection\BaseConnection;
use App\Repository\Primary\UserConnectTokenRepository;
use Nette\Application\AbortException;
use Tracy\Debugger;
use Tracy\ILogger;

class DiscordConnection extends BaseConnection
{

    public function __construct
    (
        ?int $userId,
        DiscordUserConnectFacade $userConnectFacade
    )
    {
        parent::__construct($userId, $userConnectFacade);
    }

    /**
     * @inheritDoc
     * @throws AbortException
     */
    public function handleGenerateToken(): void
    {
        $message = new \stdClass();
        try
        {
            $token = $this->userConnectFacade->generateToken($this->userId);
            $message->type = EFlashMessageType::MODAL_INFO;
            $message->title = 'Propojení s Discord serverem';
            $message->message = 'Pro propojení na serveru použijte následující příkaz: <br><br> <code>/link token:' . $token[UserConnectTokenRepository::COLUMN_TOKEN] . '</code>';
        }
        catch (\Exception $exception)
        {
            Debugger::log($exception, ILogger::EXCEPTION);
            $message->type = EFlashMessageType::MODAL_WARNING;
            $message->title = 'Chyba';
            $message->message = 'Při zpracování požadavku nastal chyba';
        }
        
        $this->presenter->flashMessage($message);
        
        if ($this->presenter->isAjax())
        {
            $this->presenter->redrawControl('flashes');
        }
        else
        {
            $this->redirect('default');
        }
    }
}