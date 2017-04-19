<?php

namespace Framework\Common;

/**
 * Class Param.
 *
 * @category PHP
 */
class Param
{
	private function __construct()
	{
		// It should never be used.
	}

	public static function get($name = '')
	{
		if (trim($name) === '') {
			$params = $_GET;
			foreach ($params as $key => $value) {
				$params[$key] = self::processParam($value);
			}

			return $params;
		}

		$param = isset($_GET[$name]) ? self::processParam($_GET[$name]) : '';

		return $param;
	}

	public static function post($name = '')
	{
		if (trim($name) === '') {
			$params = $_POST;
			foreach ($params as $key => $value) {
				$params[$key] = self::processParam($value);
			}

			return $params;
		}

		$param = isset($_POST[$name]) ? self::processParam($_POST[$name]) : '';

		return $param;
	}

	public static function request($name = '')
	{
		if (trim($name) === '') {
			$params = $_REQUEST;
			foreach ($params as $key => $value) {
				$params[$key] = self::processParam($value);
			}

			return $params;
		}

		$param = isset($_REQUEST[$name]) ? self::processParam($_REQUEST[$name]) : '';

		return $param;
	}

	public static function argv($num = '')
	{
		if (!isset($GLOBALS['argv'])) {
			return array();
		}

		if (trim($num) === '') {
			return $GLOBALS['argv'];
		}

		if (!TypeValidate::isPositiveInteger($num)) {
			return array();
		}

		return $GLOBALS['argv'][$num];
	}

	public static function processParam($data)
	{
		return $data;
	}
}

// end of script
