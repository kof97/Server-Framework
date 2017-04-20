<?php

namespace inc;

use \Exception;
use Framework\Resource\Route;

/**
 * Class Route.
 *
 * @category PHP
 */
class Router extends Route
{
	public function run()
	{
		$class = 'resource\\' . $this->resource;

		if (!class_exists($class)) {
			throw new Exception("The class ['{$class}'] is not exist");
		}

		$obj = new $class();

		$method = $this->act;

		if (!method_exists($obj, $method)) {
			throw new Exception("The method ['{$method}'] is not found in class ['{$class}']");
		}

		$code = 0;
		$msg = '';
		$res = false;

		if (!$code) {
			try {
				call_user_func(array($obj, $method));
			} catch (Exception $e) {
				$code = $e->getCode();
				$msg = $e->getMessage();
			}

			if ($code) {
				echo $msg . PHP_EOL;
			}
		}
	}
}

// end of script
