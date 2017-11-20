<?php

namespace Framework\Core;

use \Exception;
use inc\exception\ApiException;
use Framework\Exception\FException;
use Framework\Base\RouteInterface;

/**
 * Class Monitor.
 *
 * @category PHP
 * @author   Arno <1048434786@qq.com>
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

	public function init()
	{
		$err = null;
		$data = '';

		try {
			$data = $this->run();
		} catch (FException $e) {
			$err = $e;
		} catch (ApiException $e) {
			$err = $e;
		} catch (Exception $e) {
			$err = $e;
		}

		$this->display($err, $data);
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

	protected function display($err = null, $data = '')
	{
		$response = array(
			'code' => $err === null ? 0 : $err->getCode(),
			'msg'  => $err === null ? '' : $err->getMessage(),
			'data' => $data === null ? '' : $data
		);

		echo json_encode($response);
	}

	public function setRoute(RouteInterface $route)
	{
		$this->route = $route;
	}
}

// end of script
