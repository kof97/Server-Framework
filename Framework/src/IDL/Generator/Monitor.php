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
	protected $application;

	/**
	 * @var array module config.
	 */
	protected $module;

	/**
	 * @var array enum config.
	 */
	protected $enum;

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

			$this->getInterfaceInfo($module_info);
		}
	}

	protected function getInterfaceInfo($module_info) {
		foreach ($module_info['interface'] as $interface_name => $interface_info) {
			self::$trace['act'] = $interface_name;

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
		var_dump($name, $info);
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
