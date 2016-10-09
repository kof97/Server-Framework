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
        $this->route = new Route();

        $this->route->load();

        $this->resource = $this->route->getResource();
        $this->act = $this->route->getAct();
    }

    public function run()
    {
        $this->preRun();

        $code = 0;
        $msg = '';

        try {
            $this->res = $this->route->run();
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
        var_dump($this->resource);
        var_dump($this->act);
    }

    private function display()
    {

    }

    private function setRoute()
    {
        
    }
}

// end of script
