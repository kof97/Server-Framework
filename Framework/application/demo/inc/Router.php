<?php

namespace inc;

use \Exception;
use inc\exception\ApiException;
use Framework\Resource\Route;

/**
 * Class Route.
 *
 * @category PHP
 * @author   Arno <1048434786@qq.com>
 */
class Router extends Route
{
	public function run()
	{
		$res = call_user_func(array($this->class, $this->method));

		return $res;
	}
}

// end of script
