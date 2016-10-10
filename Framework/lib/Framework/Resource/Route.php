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

    /**
     * @var The function that active before run.
     */
    protected $preRunFunc;

    /**
     * @var The function that active after run.
     */
    protected $afterRunFunc;

    /**
     * @var The Get request.
     */
    protected $paramsGet = array();

    public function __construct()
    {
        $this->preRunFunc = 'preRun';
        $this->afterRunFunc = 'afterRun';

        $this->paramsGet = $_GET;
    }

    public function load()
    {
        $params = $this->paramsGet;

        $this->resource = isset($params['mod']) ? $this->processClassName($params['mod']) : '';
        $this->act = isset($params['act']) ? $this->processClassName($params['act']) : '';

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
        $res = false;

        try {
            $res = call_user_func(array($obj, $this->preRunFunc));
        } catch (Exception $e) {
            $code = $e->getCode();
            $msg = $e->getMessage();
            echo $msg . PHP_EOL;
        }

        if (!$code) {
            try {
                call_user_func(array($obj, $method));
            } catch (Exception $e) {
                $code = $e->getCode();
                $msg = $e->getMessage();
            }

            if ($code) {
                echo $msg . PHP_EOL;
            }
        }

        call_user_func(array($obj, $this->afterRunFunc));
    }

    private function processClassName($name)
    {
        $name = strtr($name, '_', ' ');

        $class_name = str_replace(' ', '', ucwords($name));

        return $class_name;
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
