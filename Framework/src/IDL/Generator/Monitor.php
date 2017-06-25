<?php

namespace IDL\Generator;

use IDL\Generator\Common;

/**
 * Class idl Generator.
 *
 * @category PHP
 * @author   Arno <1048434786@qq.com>
 */
class Monitor
{

	/**
	 * @var array application config.
	 */
	protected $application = array();

	/**
	 * @var array module config.
	 */
	protected $module = array();

	/**
	 * @var array enum config.
	 */
	protected $enum = array();

	/**
	 * @var array The current module.
	 */
	protected $moduleInfo = array();

	/**
	 * @var array The current interface.
	 */
	protected $interfaceInfo = array();

	/**
	 * @var array trace info.
	 */
	public static $trace = array(
		'mod' => '',
		'act' => '',
		'param' => '',
		'sub_param' => '',
	);

	function __construct($conf) {
		(isset($conf['idl']) && is_dir($conf['idl'])) || die('"idl" in config is not set or not exist');

		$this->prepare($conf['idl']);

		$this->run();
	}

	/**
	 * run.
	 */
	protected function run() {
		$this->getModuleInfo();
	}

	protected function getModuleInfo() {
		foreach ($this->module as $module_name => $module_info) {
			self::$trace['mod'] = $module_name;
			$this->moduleInfo = $module_info;

			$this->getInterfaceInfo();
		}
	}

	protected function getInterfaceInfo() {
		foreach ($this->moduleInfo['interface'] as $interface_name => $interface_info) {
			self::$trace['act'] = $interface_name;
			$this->interfaceInfo = $interface_info;

			$method = $interface_info['method'];
			$request = $interface_info['request'];
			$response = $interface_info['response'];

			foreach ($request as $name => $info) {
				$element_info = Common::sort($this->getElementInfo($name, $info));
				var_dump($element_info);
			}

			foreach ($response as $name => $info) {
				$this->getElementInfo($name, $info);
			}
		}
	}

	protected function getElementInfo($name, $info) {
		self::$trace['param'] = $name;

		switch ($info['type']) {
			case 'integer':
				$param_info = $this->processInt($name, $info);
				break;

			case 'int32':
				$param_info = $this->processInt($name, $info, '32');
				break;

			case 'int64':
				$param_info = $this->processInt($name, $info, '64');
				break;

			case 'string':
				$param_info = $this->processString($name, $info);
				break;

			case 'array':
				$param_info = $this->processArray($name, $info);
				break;

			case 'struct':
				$param_info = $this->processStruct($name, $info);
				break;

			default:
				$param_info = $this->getTypes($info['type']);
				break;
		}

		$param_info['name'] = $name;

		return $param_info;
	}

	protected function getTypes($name) {
		isset($types) || isset($this->interfaceInfo['types'][$name]) && $types = $this->interfaceInfo['types'][$name];
		isset($types) || isset($this->moduleInfo['types'][$name]) && $types = $this->moduleInfo['types'][$name];
		isset($types) || isset($this->application['application']['types'][$name]) && $types = $this->application['application']['types'][$name];

		$res = $this->getElementInfo($name, $types);

		return $res;
	}

	protected function processElement($name, $types) {

	}

	protected function processInt($name, $info, $type = '') {
		$res = array();

		$min = 0;
		switch ($type) {
			case '32':
				$max = 4294967295;
				break;

			case '64':

			default:
				$max = 9223372036854775807;
				break;
		}

		$res['validate']['Integer']['checkMin'][] = isset($info['validate']['min']) ? max($info['validate']['min'], $min) : $min;
		$res['validate']['Integer']['checkMax'][] = isset($info['validate']['max']) ? min($info['validate']['max'], $max) : $max;
		$res['type'] = $info['type'];

		return $res;
	}

	protected function processString($name, $info) {
		$res = array();

		$min_length = 0;
		$max_length = 255;

		$res['validate']['Enum']['checkMinLength'][] = isset($info['validate']['min_length']) ? max($info['validate']['min_length'], $min_length) : $min_length;
		$res['validate']['Enum']['checkMaxLength'][] = isset($info['validate']['max_length']) ? $info['validate']['max_length'] : $max_length;
		$res['type'] = $info['type'];

		return $res;
	}

	protected function processArray($name, $info) {

	}

	protected function processStruct($name, $info) {
		$res = array();

		foreach ($info['element'] as $key => $value) {
			self::$trace['sub_param'] = $key;

			$param = $this->getElementInfo($name, $value);
			// var_dump($param);
		}

		// var_dump($res);
	}

	/**
	 * prepare.
	 *
	 * @param string $idl_dir
	 */
	protected function prepare($idl_dir) {
		$this->prepareApplication($idl_dir);
		$this->prepareEnum($idl_dir);
		$this->prepareModule($idl_dir);
	}

	/**
	 * prepare application.
	 *
	 * @param string $idl_dir
	 */
	protected function prepareApplication($idl_dir) {
		$file = $idl_dir . DIRECTORY_SEPARATOR . 'application.json';
		is_file($file) || die('Not found the "application.json"');
		$this->application = json_decode(file_get_contents($file), true);
	}

	/**
	 * prepare enum.
	 *
	 * @param string $idl_dir
	 */
	protected function prepareEnum($idl_dir) {
		$file = $idl_dir . DIRECTORY_SEPARATOR . 'enum.json';
		is_file($file) || die('Not found the "enum.json"');
		$this->enum = json_decode(file_get_contents($file), true);
	}

	/**
	 * prepare module info.
	 *
	 * @param string $idl_dir
	 */
	protected function prepareModule($idl_dir) {
		$file_list = glob($idl_dir . DIRECTORY_SEPARATOR . '*.json');

		foreach ($file_list as $file) {
			if (strpos($file, 'application.json') !== false || strpos($file, 'enum.json') !== false) {
				continue;
			}

			$module = json_decode(file_get_contents($file), true);

			$this->module[$module['module']['name']] = $module['module'];
		}
	}
}

// end of script
