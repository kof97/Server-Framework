<?php

namespace Framework\Core;

use \Exception;
use Resource;

/**
 * Class Router.
 *
 * @category PHP
 */
class Router
{
    protected static $router;

    function __construct()
    {
        
    }

    public static function init()
    {

    }

    public function run()
    {
        $obj = new Resource\Test();
        call_user_func(array($obj, 'run'));
    }
}

// end of script
