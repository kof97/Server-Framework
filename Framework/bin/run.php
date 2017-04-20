<?php

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'etc/bootstrap.php';
require ROOT . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'loader.php';

$test = new Server\Shell\Master();

$test->init($argc, $argv);

// end of script
