<?php

namespace resource;

use model\K;
use \Exception;
use inc\exception\ApiException;
use Framework\Exception\FException;

/**
 *
 */
class Kof
{
	function __construct()
	{
		// echo 654;
	}

	public function read() {
		// K::write();
		throw new Exception('SYSTEM_BUSY');
		

		return 321;
	}
}

// end of script
