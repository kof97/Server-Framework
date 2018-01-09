<?php

require dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR . 'bootstrap.php';
require ROOT . 'src' . DIRECTORY_SEPARATOR . 'loader.php';

$app = 'api';

$config = array(
	'idl' => ROOT . '/res/idl/' . $app,
	'idl_cache' => ROOT . '/application/' . $app . '/inc/IDL',
	'error_dictionary' => ROOT . '/application/' . $app . '/inc/exception',
);

new IDL\Generator\Monitor($config);

// end of script

