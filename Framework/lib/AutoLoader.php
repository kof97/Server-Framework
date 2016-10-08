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

        $file = rtrim(__DIR__, DS) . DS . $namespace . DS . strtr($class_name, '\\', DS) . '.php';

        if (is_file($file)) {
            require $file;
        }
    }
}

spl_autoload_register(array('AutoLoader', 'load'));

// end of script
