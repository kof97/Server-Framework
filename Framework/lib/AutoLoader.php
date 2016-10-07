<?php
/**
 * Autoloader.
 *
 * @category PHP
 * @package  lib
 */

class AutoLoader
{
    /**
     * Auto loader the namespace.
     *
     * @param string $class Class name.
     *
     * @return void
     */
    public static function load($class)
    {
        foreach (NAMESPACE_LIST as $key => $value) {
            $namespace = $value;
            $prefix = $namespace . '\\';

            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                // Another namespace.
                continue;
            }

            $class_name = substr($class, $len);

            $file = rtrim(__DIR__, DS) . LIB_PATH . $namespace . DS . strtr($class_name, '\\', DS) . '.php';

            if (is_file($file)) {
                require $file;
            }
        }
    }
}

if (version_compare(PHP_VERSION, '5.4.0', '<')) {
    throw new Exception('This Framework requires PHP version 5.4 or higher.');
}

define('DS', DIRECTORY_SEPARATOR);
define('LIB_PATH', DS . 'lib' . DS);
define('NAMESPACE_LIST', array('Common', 'Framework'));

spl_autoload_register(array('AutoLoader', 'load'));

// end of script
