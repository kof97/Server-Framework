<?php

namespace Framework\Common;

/**
 * Class TypeValidate.
 *
 * @category PHP
 * @author   Arno <1048434786@qq.com>
 */
class TypeValidate
{
	private function __construct()
	{
		// It should never be used.
	}

	public static function isPositiveInteger($num)
	{
		if (!self::isInteger($num)) {
			return false;
		}

		if ($num < 0) {
			return false;
		}

		return true;
	}

	public static function isInteger($num)
	{

	}
}

// end of script
