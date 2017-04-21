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
	protected $response;

	public function __construct()
	{
		// init
	}

	public function init() {
		$code = 0;
		$msg = '';

		try {
			$this->run();
		} catch (Exception $e) {
			$code = $e->getCode();
			$msg = $e->getMessage();
			
			$this->response = json_encode(array('code' => $code, 'msg' => $msg));
		}

		$this->display();
	}

	protected function run()
	{
		$route = $this->route;

		$route->load();

		$this->resource = $route->getResource();
		$this->act = $route->getAct();

		$this->preRun();
		$this->response = $route->run();
		$this->afterRun();
	}

	private function preRun()
	{

	}

	private function afterRun()
	{

	}

	private function display()
	{
		echo $this->response;
	}

	public function setRoute(RouteInterface $route)
	{
		$this->route = $route;
	}
}

// end of script
