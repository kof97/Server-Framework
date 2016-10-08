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
    protected $preRunFunc;

    protected $afterRunFunc;

    public function __construct()
    {
        $this->preRunFunc = 'preRun';
        $this->afterRunFunc = 'afterRun';
    }

    public function run()
    {
        $obj = new Resource\Test();

        try {
            call_user_func(array($obj, $this->preRunFunc));
        } catch (Exception $e) {
            
        }

        try {
            call_user_func(array($obj, 'run'));
        } catch (Exception $e) {
            
        }

        call_user_func(array($obj, $this->afterRunFunc));

    }
}

// end of script
