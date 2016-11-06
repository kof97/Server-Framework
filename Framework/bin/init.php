<?php

if (version_compare(PHP_VERSION, '5.4.0', '<')) {
    // throw new Exception('This Framework requires PHP version 5.4 or higher.');
}

$root = dirname(__DIR__) . DIRECTORY_SEPARATOR;
$conf = $root . 'conf' . DIRECTORY_SEPARATOR . 'server.ini';

require $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'AutoLoader.php';

Framework\Shell\Cmd::init($conf);

// end of script
