<?php

namespace Server\Shell;

use \Exception;
use Framework\Common\File;

/**
 * Class Cmd.
 *
 * @category PHP
 */
class Cmd
{
	private function __construct()
	{
		// It should never be used.
	}

	/**
	 * Cli init.
	 *
	 * @param string $conf Config path.
	 *
	 * @return void
	 */
	public static function init()
	{
		$run = ROOT . 'run';
		$log = ROOT . 'log';

		is_dir($run) || File::makeDir($run);
		is_dir($log) || File::makeDir($log);
	}
}

// end of script
