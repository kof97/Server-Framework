<?php

namespace Framework\Core;

use \Exception;
use Framework\Base\RouteInterface;

/**
 * Class Monitor.
 *
 * @category PHP
 */
class Monitor
{
    /**
     * @var The instance of Route.
     */
    protected $route;

    /**
     * @var The request resource.
     */
    protected $resource;

    /**
     * @var The request act.
     */
    protected $act;

    /**
     * @var The final response.
     */
    protected $res;

    public function __construct()
    {
        // init
    }

    public function run()
    {
        $route = $this->route;

        $route->load();

        $this->resource = $route->getResource();
        $this->act = $route->getAct();

        $this->preRun();

        $code = 0;
        $msg = '';

        try {
            $this->res = $route->run();
        } catch (Exception $e) {
            $code = $e->getCode();
            $msg = $e->getMessage();
            echo $msg . PHP_EOL;
        }

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

    public function setRoute(RouteInterface $route)
    {
        $this->route = $route;
    }
}

// end of script
