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
		$code = 0;
		$msg = '';
		$res = false;

		if (!$code) {
			try {
				$res = call_user_func(array($this->class, $this->method));
			} catch (Exception $e) {
				$code = $e->getCode();
				$msg = $e->getMessage();
			}

			if ($code) {
				echo $msg . PHP_EOL;
			}
		}

		return $res;
	}
}

// end of script
