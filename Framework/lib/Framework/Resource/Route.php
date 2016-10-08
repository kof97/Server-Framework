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
	protected $resource;

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
