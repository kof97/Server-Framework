<?php

namespace Framework;

use \Exception;
use Framework\Common\File;
use Framework\Common\Loader;
use Framework\Core\Router;

/**
 * Class Application.
 *
 * @category PHP
 */
class Application
{
    private function __construct()
    {
        // It should never be used.
    }

    public static function run($conf)
    {
        $conf = parse_ini_file($conf, true);

        $mode = isset($conf['base']['mode']) ? $conf['base']['mode'] : 'restful';
        $root = isset($conf['base']['root']) ? $conf['base']['root'] : '';

        if (!is_dir($root)) {
            throw new Exception("Please check your config, '($root)' is not exist");
        }

        if (!isset($conf[$mode])) {
            throw new Exception("Not found the mode ['{$mode}']");
        }

        $conf = $conf[$mode];

        switch ($mode) {
            case 'restful':
                self::runRestful($conf, $root);
                break;

            default:
                break;
        }
    }

    private static function runRestful($conf, $root)
    {
        $root = rtrim($root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $restful_root = $root . (isset($conf['restful_root']) ? $conf['restful_root'] : 'restful');

        if (!is_dir($restful_root)) {
            throw new Exception('The dir is not exist, please run "php bin/init.php" first');
        }

        Loader::register(array('Resource', 'Model'), $restful_root);

        $router = new Router();

        $router->run();
    }
}

// end of script
