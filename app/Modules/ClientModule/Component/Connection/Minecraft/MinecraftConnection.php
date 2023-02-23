<?php

namespace App\Modules\ClientModule\Component\Connection\Minecraft;

use App\Enum\EFlashMessageType;
use App\Modules\ApiModule\Model\User\Connector\BaseUserConnectFacade;
use App\Modules\ApiModule\Model\User\Connector\MinecraftUserConnectFacade;
use App\Modules\ClientModule\Component\Connection\BaseConnection;
use App\Repository\Primary\UserConnectTokenRepository;
use Nette\Application\AbortException;
use Tracy\Debugger;
use Tracy\ILogger;

class MinecraftConnection extends BaseConnection
{
    public function __construct(
        ?int $userId,
        MinecraftUserConnectFacade $userConnectFacade
    ) {
        parent::__construct($userId, $userConnectFacade);
    }

    /**
     * @throws AbortException
     */
    public function handleGenerateToken(): void
    {
        $message = new \stdClass();
        try {
            $token = $this->userConnectFacade->generateToken($this->userId);
            $message->type = EFlashMessageType::MODAL_INFO;
            $message->title = 'Propojení s Minecraft serverem';
            $message->message = 'Pro propojení na serveru použijte následující příkaz: <br><br> <code>/connectweb ' . $token[UserConnectTokenRepository::COLUMN_TOKEN] . '</code>';
        } catch (\Exception $exception) {
            Debugger::log($exception, ILogger::EXCEPTION);
            $message->type = EFlashMessageType::MODAL_WARNING;
            $message->title = 'Chyba';
            $message->message = 'Při zpracování požadavku nastala chyba.';
        }

        $this->presenter->flashMessage($message);

        if ($this->presenter->isAjax()) {
            $this->presenter->redrawControl('flashes');
            $this->redrawControl('connection');
        } else {
            $this->presenter->redirect('default');
        }
    }
}
