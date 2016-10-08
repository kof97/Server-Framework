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
    protected $route;

    protected $resource;

    protected $act;

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
}

// end of script
