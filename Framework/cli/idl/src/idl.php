<?php

require dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR . 'bootstrap.php';
require ROOT . 'src' . DIRECTORY_SEPARATOR . 'loader.php';

$config = array(
	'idl' => ROOT . '/application/demo/etc/idl',
	'idl_config' => ROOT . '/application/demo/inc/IDL',
);

new IDL\Generator\Monitor($config);

// end of script
