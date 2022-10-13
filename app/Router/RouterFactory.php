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
		$router = new RouteList;
        $router->addRoute('/employers', 'Employers:default');
        $router->addRoute('/employers/edit/<id>', 'Employers:edit');
        $router->addRoute('/employers/create', 'Employers:create');
        $router->addRoute('/employers/delete/<id>', 'Employers:remove');

        $router->addRoute('/regenerate-employers-structure', 'Employers:regenerate');

		$router->addRoute('<presenter>/<action>[/<id>]', 'Employers:default');
		return $router;
	}
}
