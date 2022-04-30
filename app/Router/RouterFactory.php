<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList();

        $router[] = $adminRouter = new RouteList('Admin');
        $adminRouter[] = new Nette\Application\Routers\Route('[<locale=cs cs|en>/]admin/<presenter>/<action>[/<id>]', 'Default:default');

        $router[] = $apiRouter = new RouteList('Api');
        $apiRouter[] = new Nette\Application\Routers\Route('api/<presenter>/<action>[/<id>]', 'Default:default');

		$router[] = $frontRouter = new RouteList('Web');
        $frontRouter[] = new Nette\Application\Routers\Route('[<locale=cs cs|en>/]<presenter>/<action>[/<id>]','Homepage:default');



		return $router;
	}
}
