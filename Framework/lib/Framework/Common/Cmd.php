<?php

namespace Framework\Common;

use \Exception;
use Framework\Common\File;

/**
 * Class Cmd.
 *
 * @category PHP
 */
class Cmd
{
    private function __construct()
    {
        // It should never be used.
    }

    /**
     * Cli init.
     *
     * @param string $conf Config path.
     *
     * @return void
     */
    public static function init($conf)
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
                self::initRestful($conf, $root);
                break;

            default:
                break;
        }
    }

    private static function initRestful($conf, $root)
    {
        $root = rtrim($root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $resource_root = $root . (isset($conf['resource_root']) ? $conf['resource_root'] : 'restful/Resource');
        $model_root = $root . (isset($conf['model_root']) ? $conf['model_root'] : 'restful/Model');

        is_dir($resource_root) || File::makeDir($resource_root);
        is_dir($model_root) || File::makeDir($model_root);

        echo PHP_EOL . 'Init restful OK' . PHP_EOL;
    }
}

// end of script
