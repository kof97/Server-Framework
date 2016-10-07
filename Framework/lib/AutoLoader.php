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
        $namespace = 'Framework';
        $prefix = $namespace . '\\';

        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            throw new Exception("Namespace ['{$namespace}'] is not exist");
        }

        $class_name = substr($class, $len);

        $file = rtrim(__DIR__, DS) . DS . $namespace . DS . strtr($class_name, '\\', DS) . '.php';

        if (is_file($file)) {
            require $file;
        }
    }
}

if (version_compare(PHP_VERSION, '5.4.0', '<')) {
    throw new Exception('This Framework requires PHP version 5.4 or higher.');
}

define('DS', DIRECTORY_SEPARATOR);

spl_autoload_register(array('AutoLoader', 'load'));

// end of script
