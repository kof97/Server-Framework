<?php

namespace Framework\Resource;

use Resource;

/**
 * Class Route.
 *
 * @category PHP
 */
class Route
{
    public function __construct()
    {

    }

    public function run()
    {
    	$obj = new Resource\Test();

        try {
            // call_user_func(array($obj, $this->preRunFunc));
        } catch (Exception $e) {
            
        }

        try {
            call_user_func(array($obj, 'run'));
        } catch (Exception $e) {
            
        }

        // call_user_func(array($obj, $this->afterRunFunc));
    }
}

// end of script
