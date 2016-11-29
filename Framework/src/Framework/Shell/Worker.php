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

	protected $socketName = '';

    public function __construct($socket_name = '')
    {
    	$info = parse_url($socket_name);

    	var_dump($info);
    }

    public function run()
    {

        while (true) {
            sleep(5);
        }

        $this->listen();
    }

    protected function listen()
    {





    	if (Master::$globalEvent) {
    		if (Master::$protocol === 'udp') {
    			Master::$globalEvent->add($this->socket, EventInterface::EV_READ, array($this, 'acceptUdpConnection'));
    		} else {
    			Master::$globalEvent->add($this->socket, EventInterface::EV_READ, array($this, 'acceptConnection'));
    		}
    	}
    }
}

// end of script
