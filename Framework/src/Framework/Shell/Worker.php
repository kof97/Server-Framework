<?php

namespace Framework\Shell;

/**
 * Class Worker.
 *
 * @category PHP
 */
class Worker
{

    public function __construct()
    {

    }

    public function run()
    {

    	var_dump(Master::$globalEvent);

        while (true) {
            sleep(5);
        }
    }
}

// end of script
