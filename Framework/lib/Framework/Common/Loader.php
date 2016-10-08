<?php

namespace Framework\Common;

/**
 * Class Loader.
 *
 * @category PHP
 */
class Loader
{
    private function __construct()
    {
        // It should never be used.
    }

    public static function register($path)
    {
        spl_autoload_register(function ($class) use ($path) {
            $file = $path . DIRECTORY_SEPARATOR . $class . '.php';
            if (is_file($file)) {
                require $file;
            }
        });
    }

    public static function batchRegister($path_list = array())
    {
        foreach ($path_list as $path) {
            spl_autoload_register(function ($class) use ($path) {
                $file = $path . DIRECTORY_SEPARATOR . $class . '.php';
                if (is_file($file)) {
                    require $file;
                }
            });
        }
    }
}

// end of script
