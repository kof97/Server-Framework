<?php

namespace Framework;

use \Loader;
use \Exception;
use Framework\Core\Monitor;
use Framework\Resource\Route;

/**
 * Class Application.
 *
 * @category PHP
 * @author   Arno <1048434786@qq.com>
 */
class Application
{
	private function __construct()
	{
		// It should never be used.
	}

	public static function run($root)
	{
		Loader::register(array('resource', 'model', 'inc'), $root);

		$monitor = new Monitor();

		$route_class = 'inc\Router';
		if (class_exists($route_class)) {
			$route = new $route_class;
		} else {
			$route = new Route;
		}

		$monitor->setRoute($route);
		$monitor->init();
	}
}

// end of script
