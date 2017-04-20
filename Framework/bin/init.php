<?php

if (version_compare(PHP_VERSION, '5.4.0', '<')) {
	// throw new Exception('This Framework requires PHP version 5.4 or higher.');
}

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'etc/bootstrap.php';
require ROOT . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'loader.php';

Server\Shell\Cmd::init();

// end of script
