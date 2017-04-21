<?php

namespace Framework\Resource;

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
	 * @var The request class.
	 */
	protected $class;

	/**
	 * @var The request method.
	 */
	protected $method;

	/**
	 * @var The Get request.
	 */
	protected $paramsGet = array();

	public function __construct()
	{
		$this->paramsGet = $_GET;
	}

	public function load()
	{
		$params = $this->paramsGet;

		$this->resource = isset($params['mod']) ? $this->processClassName($params['mod']) : '';
		$this->act = isset($params['act']) ? lcfirst($this->processClassName($params['act'])) : '';

		$this->headers = getallheaders();

		$this->prepare();
	}

	public function run()
	{
		$res = call_user_func(array($this->class, $this->method));

		return $res;
	}

	protected function prepare() {
		$class = 'resource\\' . $this->resource;

		if (!class_exists($class)) {
			throw new Exception('Module not exist');
		}

		$this->class = new $class();

		$this->method = $this->act;

		if (!method_exists($this->class, $this->method)) {
			throw new Exception('Act not exist');
		}
	}

	protected function processClassName($name)
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
