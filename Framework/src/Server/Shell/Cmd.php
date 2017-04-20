<?php

namespace Server\Shell;

use \Exception;
use Server\Common\File;

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

		if (!isset($conf[$mode])) {
			throw new Exception("Not found the mode ['{$mode}']");
		}

		$conf = $conf[$mode];

		switch ($mode) {
			case 'restful':
				self::initRestful($conf, $root);
				break;

			default:
				break;
		}
	}

	private static function initRestful($conf, $root)
	{
		$restful_root = $root . 'restful' . DIRECTORY_SEPARATOR;

		is_dir($restful_root) || File::makeDir($restful_root);

		$resource_root = $restful_root . 'Resource';
		$model_root = $restful_root . 'Model';

		is_dir($resource_root) || File::makeDir($resource_root);
		is_dir($model_root) || File::makeDir($model_root);

		chmod($restful_root, 0644);
		chmod($resource_root, 0644);
		chmod($model_root, 0644);

		echo PHP_EOL . 'Init restful OK' . PHP_EOL;
	}
}

// end of script
