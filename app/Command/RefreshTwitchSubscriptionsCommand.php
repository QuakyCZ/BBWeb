<?php

namespace App\Command;


use App\Modules\ApiModule\Model\User\Connector\TwitchUserConnectFacade;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use Tracy\Debugger;

class RefreshTwitchSubscriptionsCommand extends Command
{


    protected static $defaultName = 'refresh-twitch-subscriptions';

    public function __construct(
        private readonly TwitchUserConnectFacade $twitchUserConnectFacade,
    )
    {
        parent::__construct('refresh-twitch-subscriptions');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->twitchUserConnectFacade->refreshSubscriptions();
        } catch (GuzzleException $exception) {
            if ($exception->getCode() !== 404) {
                Debugger::log($exception, 'twitch');
            }
        } catch (Throwable $e) {
            Debugger::log($e, 'twitch');
            $output->writeln($e->getMessage());
            return Command::FAILURE;
        }

        return 0;
    }
}