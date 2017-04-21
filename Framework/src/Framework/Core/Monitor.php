<?php

namespace Framework\Core;

use \Exception;
use Framework\Exception\FException;
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

	public function __construct()
	{
		// init
	}

	public function init() {
		$code = 0;
		$msg = '';
		$data = '';

		try {
			$data = $this->run();
		} catch (FException $e) {
			$code = $e->getCode();
			$msg = $e->getMessage();
		} catch (Exception $e) {

		}

		$this->display($code, $msg, $data);
	}

	protected function run()
	{
		$route = $this->route;

		$route->load();

		$this->resource = $route->getResource();
		$this->act = $route->getAct();

		$this->preRun();
		$response = $route->run();
		$this->afterRun();

		return $response;
	}

	protected function preRun()
	{

	}

	protected function afterRun()
	{

	}

	protected function display($code = 0, $msg = '', $data = '')
	{
		$response = array(
			'code' => $code,
			'msg'  => $msg,
			'data' => $data
		);

		echo json_encode($response);
	}

	public function setRoute(RouteInterface $route)
	{
		$this->route = $route;
	}
}

// end of script
