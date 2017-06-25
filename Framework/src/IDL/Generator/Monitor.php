<?php

namespace IDL\Generator;

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
				$this->getElementInfo($name, $info);
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

		return $param_info;
	}

	protected function getTypes($name) {
		isset($types) || isset($this->interfaceInfo['types'][$name]) && $types = $this->interfaceInfo['types'][$name];
		isset($types) || isset($this->moduleInfo['types'][$name]) && $types = $this->moduleInfo['types'][$name];
		isset($types) || isset($this->application['application']['types'][$name]) && $types = $this->application['application']['types'][$name];

		$this->getElementInfo($name, $types);
	}

	protected function processElement($name, $types) {

	}

	protected function processInt($name, $info, $type = '') {

	}

	protected function processString($name, $info) {

	}

	protected function processArray($name, $info) {

	}

	protected function processStruct($name, $info) {
		$res = array();

		foreach ($info['element'] as $key => $value) {
			self::$trace['sub_param'] = $key;

			$param = $this->getElementInfo($name, $value);
			var_dump($param);
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
