<?php

/**
 * Class Loader.
 *
 * @category PHP
 * @author   Arno <1048434786@qq.com>
 */
class Loader
{
	private function __construct()
	{
		// It should never be used.
	}

	public static function register($namespace_list = array(), $root)
	{
		if (!is_array($namespace_list)) {
			$namespace_list = array($namespace_list);
		}

		foreach ($namespace_list as $namespace) {
			spl_autoload_register(function ($class) use ($namespace, $root) {
				$prefix = $namespace . '\\';

				$len = strlen($prefix);
				if (strncmp($prefix, $class, $len) !== 0) {
					return false;
				}

				$class_name = substr($class, $len);

				$file = $root . DIRECTORY_SEPARATOR . $namespace . DIRECTORY_SEPARATOR . strtr($class_name, '\\', DIRECTORY_SEPARATOR) . '.php';

				if (is_file($file)) {
					require $file;
				}
			});
		}
	}
}

defined('ROOT') || define('ROOT', dirname(__dir__) . DIRECTORY_SEPARATOR);

Loader::register(array('Framework', 'Server', 'IDL'), ROOT . 'src');

// end of script
