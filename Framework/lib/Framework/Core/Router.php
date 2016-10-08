<?php

namespace Framework\Core;

use \Exception;
use Framework\Resource\Route;

/**
 * Class Router.
 *
 * @category PHP
 */
class Router
{
    protected $preRunFunc;

    protected $afterRunFunc;

    protected $route;

    protected $act;

    protected $res;

    public function __construct()
    {
        $this->route = new Route();
    }

    public function run()
    {
        $this->preRun();

        $this->route->run();

        $this->afterRun();
        $this->display();
    }

    private function preRun()
    {

    }

    private function afterRun()
    {

    }

    private function display()
    {

    }
}

// end of script
