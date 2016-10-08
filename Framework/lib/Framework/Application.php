<?php

namespace Framework;

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

    public static function init($conf)
    {
        $conf = parse_ini_file($conf, true);

        $mode = isset($conf['base']['mode']) ? $conf['base']['mode'] : 'restful';

        if (!isset($conf[$mode])) {
            throw new Exception("Not found the mode ['{$mode}']");
        }

        $conf = $conf[$mode];

        switch ($mode) {
            case 'restful':
                self::initRestful($conf);
                break;

            default:
                break;
        }
    }

    private static function initRestful($conf)
    {
        $resource_root = ROOT . isset($conf['resource_root']) ? $conf['resource_root'] : 'restful/Resource';
        $model_root = ROOT . isset($conf['model_root']) ? $conf['model_root'] : 'restful/Model';

        is_dir($resource_root) || self::makeDir($resource_root);
        is_dir($model_root) || self::makeDir($model_root);
    }
}

// end of script
