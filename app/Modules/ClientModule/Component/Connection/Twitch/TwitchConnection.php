<?php

namespace App\Modules\ClientModule\Component\Connection\Twitch;

use App\Enum\EFlashMessageType;
use App\Modules\ApiModule\Model\User\Connector\MinecraftUserConnectFacade;
use App\Modules\ApiModule\Model\User\Connector\TwitchUserConnectFacade;
use App\Modules\ClientModule\Component\Connection\BaseConnection;
use App\Repository\Primary\UserRepository;
use GuzzleHttp\Exception\GuzzleException;
use NewTwitchApi\NewTwitchApi;
use Throwable;
use Tracy\Debugger;

class TwitchConnection extends BaseConnection
{

    public function __construct(
        ?int $userId,
        private readonly TwitchUserConnectFacade $twitchUserConnectFacade,
        private readonly MinecraftUserConnectFacade $minecraftUserConnectFacade,
    )
    {
        parent::__construct($userId, $twitchUserConnectFacade);
    }

    public function render(): void
    {
        $this->template->minecraftConnected = $this->minecraftUserConnectFacade->isConnected($this->userId);
        parent::render();
    }

    /**
     * @inheritDoc
     */
    public function handleGenerateToken(): void
    {
        $this->presenter->redirectUrl($this->twitchUserConnectFacade->getOauthUri());
    }

    /**
     * @return void
     * @throws \Nette\Application\AbortException
     */
    public function handleVerifySubscription(): void
    {

        if (!$this->minecraftUserConnectFacade->isConnected($this->userId)) {
            $this->presenter->flashMessage('Nejprve se musíš připojit na Minecraftem!', EFlashMessageType::ERROR);
        } else {
            try {
                $this->twitchUserConnectFacade->refreshSubscriptionForUser($this->presenter->user->id);
                $this->presenter->flashMessage('Odběr byl úspěšně ověřen. Můžeš jít hrát na Subserver!', EFlashMessageType::SUCCESS);
            } catch (GuzzleException $exception) {
                $this->presenter->flashMessage('Nepodařilo se ověřit odběr.', EFlashMessageType::ERROR);
                if ($exception->getCode() !== 404) {
                    Debugger::log($exception, 'twitch');
                }
            } catch (Throwable $exception) {
                $this->presenter->flashMessage('Při zpracování požadavku nastala neznámá chyba.', EFlashMessageType::ERROR);
                Debugger::log($exception, 'twitch');
            }
        }

        if ($this->presenter->isAjax()) {
            $this->redrawControl();
            $this->presenter->redrawControl();
        } else {
            $this->presenter->redirect('this');
        }
    }
}