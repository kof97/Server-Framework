<?php

namespace inc;

use inc\exception\ApiException;
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
		$res = false;

		try {
			var_dump(1111111111111);
			$res = call_user_func(array($this->class, $this->method));
		} catch (Exception $e) {
			var_dump(2222222222);
			$code = $e->getCode();
			$msg = $e->getMessage();
		}

		return $res;
	}
}

// end of script
