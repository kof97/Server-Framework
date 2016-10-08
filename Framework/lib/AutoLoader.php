<?php
/**
 * Autoloader.
 *
 * @category PHP
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

        $file = rtrim(__DIR__, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $namespace . DIRECTORY_SEPARATOR
                 . strtr($class_name, '\\', DIRECTORY_SEPARATOR) . '.php';

        if (is_file($file)) {
            require $file;
        }
    }
}

spl_autoload_register(array('AutoLoader', 'load'));

// end of script
