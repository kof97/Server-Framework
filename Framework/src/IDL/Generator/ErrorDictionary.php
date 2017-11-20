<?php

namespace IDL\Generator;

use IDL\Generator\Common;

/**
 * Class ErrorDictionary.
 *
 * @category PHP
 * @author   Arno <1048434786@qq.com>
 */
class ErrorDictionary
{
	protected static $separator = PHP_EOL . '    ';

	private function __construct()
	{

	}

	public static function write($error_set, $path)
	{
		$error_code = array();
		$error_msg = array();
		foreach ($error_set['error'] as $const => $value) {
			array_push($error_code, 'const ' . $const . ' = ' . $value['code'] . ';' . PHP_EOL);
			array_push($error_msg, 'const ' . $const . ' = ' . $value['message'] . ';' . PHP_EOL);
		}

		Common::write($path . DIRECTORY_SEPARATOR . 'ErrorCode.php', self::errorCode(implode($error_code, self::$separator)));
		Common::write($path . DIRECTORY_SEPARATOR . 'ErrorMessage.php', self::errorCode(implode($error_msg, self::$separator)));
	}

	protected static function errorCode($error_code)
	{
		$ret = <<<EOF
<?php

namespace inc\\exception;

class ErrorCode
{
	$error_code
}

// end of script

EOF;

		return $ret;
	}

	protected static function errorMsg($error_msg)
	{
		$ret = <<<EOF
<?php

namespace inc\\exception;

class ErrorMessage
{
	$error_msg
}

// end of script

EOF;

		return $ret;
	}
}

// end of script
