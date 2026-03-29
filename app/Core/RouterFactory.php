<?php declare(strict_types=1);

namespace App\Core;

use Nette;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;
        $router->addRoute('register', 'Sign:register');
        $router->addRoute('login', 'Sign:login');
        $router->addRoute('out', 'Sign:out');
        $router->addRoute('destination-logs/create', 'DestinationLog:create');
        $router->addRoute('destination-logs', 'DestinationLog:datagrid');
        $router->addRoute('<presenter>/<action>[/<id>]', 'Home:default');
        return $router;
	}
}
