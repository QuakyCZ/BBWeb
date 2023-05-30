<?php

declare(strict_types=1);

namespace App;

use Nette\Bootstrap\Configurator;
use Tracy\Debugger;
use Tracy\NativeSession;

class Bootstrap
{
    public static function boot(): Configurator
    {
        $configurator = new Configurator();
        $appDir = dirname(__DIR__);

        //$configurator->setDebugMode('secret@23.75.345.200'); // enable for your remote IP

        $debug = (bool)(getenv('DEBUG') ?? false);

        if ($debug === true) {
            $configurator->setDebugMode(true);
            $configurator->enableTracy($appDir . '/log');
        }

        Debugger::$strictMode = true;
        Debugger::$logSeverity = E_ALL;

        $configurator->enableDebugger($appDir.'/log');


        $configurator->setTimeZone('Europe/Prague');
        $configurator->setTempDirectory($appDir . '/temp');

        $configurator->createRobotLoader()
            ->addDirectory(__DIR__)
            ->register();

        $configurator->addConfig($appDir . '/config/common.neon');
        $configurator->addConfig($appDir . '/config/local.neon');

        return $configurator;
    }
}
