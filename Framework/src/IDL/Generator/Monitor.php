<?php

namespace IDL\Generator;

use IDL\Generator\Common;
use IDL\Generator\ErrorDictionary;

/**
 * Class idl Generator.
 *
 * @category PHP
 * @author   Arno <1048434786@qq.com>
 */
class Monitor
{
	/**
	 * @var string config.
	 */
	protected $config;

	/**
	 * @var array error config.
	 */
	protected $error = array();

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

	function __construct($conf)
	{
		(isset($conf['idl']) && is_dir($conf['idl'])) || die('"idl" in config is not set or not exist');
		isset($conf['idl_config']) || die('"idl config dir" in config is not set or not exist');
		is_dir($conf['idl_config']) || mkdir($conf['idl_config']);

		$this->config = $conf;

		$this->prepare();

		$this->run();
	}

	/**
	 * run.
	 */
	protected function run()
	{
		$this->getModuleInfo();
	}

	/**
	 * read module.
	 */
	protected function getModuleInfo()
	{
		foreach ($this->module as $module_name => $module_info) {
			self::$trace['mod'] = $module_name;
			$this->moduleInfo = $module_info;

			$this->getInterfaceInfo();
		}
	}

	/**
	 * read interface.
	 */
	protected function getInterfaceInfo()
	{
		foreach ($this->moduleInfo['interface'] as $interface_name => $interface_info) {
			self::$trace['act'] = $interface_name;
			$this->interfaceInfo = $interface_info;

			$request = $interface_info['request'];
			$response = $interface_info['response'];

			$content['name'] = $interface_name;
			$content['method'] = $interface_info['method'];

			foreach ($request as $name => $info) {
				$request_info[$name] = $this->getElementInfo($name, $info);
			}

			foreach ($response as $name => $info) {
				$response_info[$name] = $this->getElementInfo($name, $info);
			}

			$content['request'] = $request_info;
			$content['response'] = $response_info;

			$this->write($content);
		}
	}

	/**
	 * write to file.
	 *
	 * @param array $data
	 */
	protected function write($data)
	{
		$file = $this->config['idl_config'] . DIRECTORY_SEPARATOR . strtolower(str_replace('_', '', self::$trace['mod']) . '_' . str_replace('_', '', self::$trace['act'])) . '.php';

		Common::write($file, '<?php' . PHP_EOL . var_export($data, true));
	}

	/**
	 * read element info.
	 *
	 * @param string $name
	 * @param array  $info
	 *
	 */
	protected function getElementInfo($name, $info)
	{
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

			case 'enum':
				$param_info = $this->processEnum($name, $info);
				break;

			case 'array':
				$param_info = $this->processArray($name, $info);
				break;

			case 'struct':
				$param_info = $this->processStruct($name, $info);
				break;

			default:
				$param_info = $this->getTypes($info['type']);
				$param_info['name'] = $info['type'];
				break;
		}

		!isset($param_info['name']) && $param_info['name'] = $name;
		isset($info['description']) && $param_info['description'] = $info['description'];

		return Common::sort($param_info);
	}

	/**
	 * find the origin types.
	 *
	 * @param string $name
	 *
	 * @param array
	 */
	protected function getTypes($name)
	{
		// todo 引用逻辑

		isset($types) || isset($this->interfaceInfo['types'][$name]) && $types = $this->interfaceInfo['types'][$name];
		isset($types) || isset($this->moduleInfo['types'][$name]) && $types = $this->moduleInfo['types'][$name];
		isset($types) || isset($this->application['application']['types'][$name]) && $types = $this->application['application']['types'][$name];

		$res = $this->getElementInfo($name, $types);

		return $res;
	}

	/**
	 * process element.
	 *
	 * @param string $name
	 * @param array  $types
	 *
	 * @param array
	 */
	protected function processElement($name, $types)
	{

	}

	/**
	 * process int info.
	 *
	 * @param string $name
	 * @param array  $info
	 * @param string $type
	 *
	 * @return array
	 */
	protected function processInt($name, $info, $type = '')
	{
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
		$res['type'] = 'integer';

		return $res;
	}

	/**
	 * process string info.
	 *
	 * @param string $name
	 * @param array  $info
	 *
	 * @return array
	 */
	protected function processString($name, $info)
	{
		$res = array();

		$min_length = 0;
		$max_length = 255;

		$res['validate']['String']['checkMinLength'][] = isset($info['validate']['min_length']) ? max($info['validate']['min_length'], $min_length) : $min_length;
		$res['validate']['String']['checkMaxLength'][] = isset($info['validate']['max_length']) ? $info['validate']['max_length'] : $max_length;

		isset($info['validate']['pattern']) && $res['validate']['String']['checkRegex'][] = $info['validate']['pattern'];

		$res['type'] = 'string';

		return $res;
	}

	/**
	 * process enum type.
	 *
	 * @param string $name
	 * @param array  $info
	 *
	 * @return array
	 */
	protected function processEnum($name, $info)
	{
		$res = array();

		$enum_all = $this->getEnumByName($info['validate']['source']);
		if (array_intersect($info['validate']['list'], $enum_all) != $info['validate']['list']) {
			die('Enum must be in source');
		} else if (empty($info['validate']['list'])) {
			$enum = $enum_all;
		} else {
			$enum = $info['validate']['list'];
		}

		$res['validate']['Enum']['checkEnum'][] = $enum;
		$res['validate']['Enum']['checkEnum'][] = $info['validate']['source'];

		return $res;
	}

	protected function processArray($name, $info)
	{
		$res = array();

		if (isset($info['repeated'])) {
			$res['repeated'] = $this->getElementInfo($name, $info['repeated']);
		}

		$res['type'] = 'array';

		return $res;
	}

	/**
	 * process struct info.
	 *
	 * @param string $name
	 * @param array  $info
	 *
	 * @return array
	 */
	protected function processStruct($name, $info)
	{
		self::$trace['param'] = $name;
		$res = array();

		$param = array();
		foreach ($info['element'] as $key => $value) {
			self::$trace['sub_param'] = $key;

			$param[$key] = $this->getElementInfo($key, $value);
		}

		$res['name'] = $name;
		$res['type'] = 'struct';
		$res['element'] = $param;

		return $res;
	}

	/**
	 * get enum.
	 *
	 * @param string $name
	 *
	 * @return array
	 */
	protected function getEnumByName($name)
	{
		return $this->enum['enumeration'][$name];
	}

	/**
	 * prepare.
	 *
	 */
	protected function prepare()
	{
		$this->processErrorMsg();
		$this->prepareApplication();
		$this->prepareEnum();
		$this->prepareModule();
	}

	/**
	 * process error dictionary
	 *
	 */
	protected function processErrorMsg()
	{
		$file = $this->config['idl'] . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'error.json';
		is_file($file) || die('Not found the "error.json"');
		$error_set = json_decode(file_get_contents($file), true);

		ErrorDictionary::write($error_set, $this->config['error_dictionary']);
	}

	/**
	 * prepare application.
	 *
	 */
	protected function prepareApplication()
	{
		$file = $this->config['idl'] . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'application.json';
		is_file($file) || die('Not found the "application.json"');
		$this->application = json_decode(file_get_contents($file), true);
	}

	/**
	 * prepare enum.
	 *
	 */
	protected function prepareEnum()
	{
		$file = $this->config['idl'] . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'enum.json';
		is_file($file) || die('Not found the "enum.json"');
		$this->enum = json_decode(file_get_contents($file), true);
	}

	/**
	 * prepare module info.
	 *
	 */
	protected function prepareModule()
	{
		$file_list = glob($this->config['idl'] . DIRECTORY_SEPARATOR . '*.json');

		foreach ($file_list as $file) {
			$module = json_decode(file_get_contents($file), true);

			$this->module[$module['module']['name']] = $module['module'];
		}
	}
}

// end of script
