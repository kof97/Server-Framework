<?php

if (version_compare(PHP_VERSION, '5.4.0', '<')) {
    // throw new Exception('This Framework requires PHP version 5.4 or higher.');
}

define('DS', DIRECTORY_SEPARATOR);

define('ROOT', dirname(__DIR__) . DS);
define('BIN_PATH', ROOT . 'bin' . DS);


$conf = ROOT . 'conf' . DS . 'server.ini';

var_dump(ROOT);

require ROOT . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'AutoLoader.php';

Framework\Application::init($conf);

// end of script
