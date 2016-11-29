<?php

namespace Framework\Shell;

/**
 * Class Worker.
 *
 * @category PHP
 */
class Worker
{
	protected $socket = null;

    public function __construct()
    {

    }

    public function run()
    {

        while (true) {
            sleep(5);
        }
    }
}

// end of script
