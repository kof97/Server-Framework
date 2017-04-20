<?php

namespace resource;

use model\K;

/**
 *
 */
class Kof
{
	function __construct()
	{
		echo 654;
	}

	public function read() {
		K::write();
	}
}

// end of script
