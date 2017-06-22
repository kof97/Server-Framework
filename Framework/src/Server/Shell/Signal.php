<?php

namespace Server\Shell;

/**
 * Class Signal.
 *
 * @category PHP
 * @author   Arno <1048434786@qq.com>
 */
final class Signal
{
	public static $signo = 0;

	protected static $ini = null;

	public static function set($signo)
	{
		self::$signo = $signo;
	}

	public static function get()
	{
		return self::$signo;
	}

	public static function reset()
	{
		self::$signo = 0;
	}
}

// end of script
