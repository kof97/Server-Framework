<?php

namespace Framework\Exception;

use \Exception;

class FException extends Exception
{
	function __construct($msg, $data = array()) {
		$this->code = constant("Framework\\Exception\\ErrorCode::{$msg}");
		$this->message = constant("Framework\\Exception\\ErrorMessage::{$msg}");
	}
}

// end of script
