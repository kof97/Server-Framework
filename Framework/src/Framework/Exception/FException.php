<?php

namespace Framework\Exception;

use \Exception;

class FException extends Exception
{
	function __construct($msg, $data = array())
	{
		if (@constant("inc\\exception\\ErrorCode::{$msg}")) {
			$code = constant("inc\\exception\\ErrorCode::{$msg}");
			$message = constant("inc\\exception\\ErrorMessage::{$msg}");
		} else {
			$code = constant("Framework\\Exception\\ErrorCode::{$msg}");
			$message = constant("Framework\\Exception\\ErrorMessage::{$msg}");
		}

		$this->code = $code;
		$this->message = $message;
	}
}

// end of script
