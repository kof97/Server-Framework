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

        $resource_root = $root . (isset($conf['resource_root']) ? $conf['resource_root'] : 'restful/Resource');
        $model_root = $root . (isset($conf['model_root']) ? $conf['model_root'] : 'restful/Model');

        if (!is_dir($resource_root) || !is_dir($model_root)) {
            throw new Exception('The dir is not exist, please run "php bin/init.php" first');
        }

        Loader::batchRegister(array($resource_root, $model_root));

        Router::init();
    }
}

// end of script
