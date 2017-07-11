<?php

require dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR . 'bootstrap.php';
require ROOT . 'src' . DIRECTORY_SEPARATOR . 'loader.php';

$app = 'demo';

$config = array(
	'idl' => ROOT . '/application/' . $app . '/etc/idl',
	'idl_config' => ROOT . '/application/' . $app . '/inc/IDL',
);

new IDL\Generator\Monitor($config);

// end of script

