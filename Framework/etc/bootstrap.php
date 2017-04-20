<?php

if (version_compare(PHP_VERSION, '5.4.0', '<')) {
	throw new Exception('This Framework requires PHP version 5.4 or higher.');
}

define('ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);

// end of script
