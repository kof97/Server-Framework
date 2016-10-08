<?php

namespace Framework\Common;

/**
 * Class File.
 *
 * @category PHP
 */
class File
{
    
    private function __construct()
    {
        // It should never be used.
    }

    public static function makeDir($dir, $mode = 0777)
    {
        $dir = strtr($dir, '/', DS);
        $dir = strtr($dir, '\\', DS);

        if (!is_dir($dir)) {
            $default = umask(0000);
            mkdir($dir, $mode, true);
            umask($default);
        }
    }
}

// end of script
