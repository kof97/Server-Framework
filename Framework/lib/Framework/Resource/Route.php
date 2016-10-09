<?php

namespace Framework\Resource;

use Resource;
use \Exception;
use Framework\Base\RouteInterface;

/**
 * Class Route.
 *
 * @category PHP
 */
class Route implements RouteInterface
{
    /**
     * @var The request resource.
     */
    protected $resource;

    /**
     * @var The request act.
     */
    protected $act;

    /**
     * @var The request headers.
     */
    protected $headers;

    public function __construct()
    {

    }

    public function load()
    {
        $this->resource = isset($_REQUEST['mod']) ? $_REQUEST['mod'] : '';
        $this->act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';

        $this->headers = getallheaders();
    }

    public function run()
    {
        $class = 'Resource\\' . $this->resource;

        if (!class_exists($class)) {
            throw new Exception("The class ['{$class}'] is not exist");
        }

        $obj = new $class();

        $method = $this->act;

        if (!method_exists($obj, $method)) {
            throw new Exception("The method ['{$method}'] is not found in class ['{$class}']");
        }

        $code = 0;
        $msg = '';

        try {
            // call_user_func(array($obj, $this->preRunFunc));
        } catch (Exception $e) {
            $code = $e->getCode();
            $msg = $e->getMessage();
        }

        if (!$code) {
            try {
                call_user_func(array($obj, $method));
            } catch (Exception $e) {
                $code = $e->getCode();
                $msg = $e->getMessage();
            }

            if ($code) {

            }
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
