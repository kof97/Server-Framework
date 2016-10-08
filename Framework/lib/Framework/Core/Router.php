<?php

namespace Framework\Core;

use \Exception;

/**
 * Class Router.
 *
 * @category PHP
 */
class Router
{
    protected static $router;

    private function __construct()
    {
        
    }

    public static function init()
    {
        self::$router = new self();

        return self::$router;
    }

    public function run()
    {
        
    }
}

// end of script
