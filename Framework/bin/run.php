<?php

$root = dirname(__DIR__) . DIRECTORY_SEPARATOR;

require $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'AutoLoader.php';

$test = new Framework\Shell\Master();

$test->init($argc, $argv);

// end of script
