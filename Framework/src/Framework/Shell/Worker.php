<?php

namespace Framework\Shell;

/**
 * Class Worker.
 *
 * @category PHP
 */
class Worker
{
    protected $globalEvent = null;

    protected $eventLoops = array(
        'libevent',
        'event',
        'ev',
        'select'
    );

    protected $eventLoopName;

    public function __construct()
    {

    }

    public function run()
    {
        $event_loop_name = $this->getEventLoopName();
        while (true) {
            sleep(5);
        }
    }

    protected function getEventLoopName()
    {
        foreach ($this->eventLoops as $name) {
            if (extension_loaded($name)) {
                $this->eventLoopName = $name;
                break;
            }
        }

        return $this->eventLoopName;
    }
}

// end of script
