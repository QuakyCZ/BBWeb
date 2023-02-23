<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

final class RouterFactory
{
    use Nette\StaticClass;

    public static function createRouter(): RouteList
    {
        $router = new RouteList();

        $router[] = $adminRouter = new RouteList('Admin');
        $adminRouter[] = new Route('[<locale=cs cs|en>/]admin/<presenter>/<action>[/<id>]', 'Default:default');

        $router[] = new Route('/api/v<version>/<package>[/<apiAction>][/<params>]', 'Api:Api:default');

        $router[] = $clientRouter = new RouteList('Client');
        $clientRouter[] = new Route('[<locale=cs cs|en/>]client/<presenter>/<action>[/<id>]', 'Dashboard:default');

        $router[] = $frontRouter = new RouteList('Web');
        $frontRouter[] = new Route('[<locale=cs cs|en>/]<presenter>/<action>[/<id>]', 'Homepage:default');



        return $router;
    }
}
