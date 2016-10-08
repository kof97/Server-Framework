<?php

namespace Framework\Resource;

use Resource;
use \Exception;

/**
 * Class Route.
 *
 * @category PHP
 */
class Route
{
    /**
     * @var The request resource.
     */
    protected $resource;

    /**
     * @var The request act.
     */
    protected $act;

    public function __construct()
    {

    }

    public function load()
    {
        $this->resource = isset($_REQUEST['mod']) ? $_REQUEST['mod'] : '';
        $this->act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';

    }

    public function run()
    {
        $class = 'Resource\\' . $this->resource;

        if (!class_exists($class)) {
            throw new Exception("The class ['{$class}'] is not exist");
        }

        $obj = new $class();

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

    public function getResource()
    {
        return $this->resource;
    }

    public function getAct()
    {
        return $this->act;
    }
}

// end of script
