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

    protected $scheme = null;

    protected $host = null;

    protected $port = null;

    protected $endpoint = null;

    protected $socketName = '';

    public function __construct($socket_name = '')
    {
        $info = parse_url($socket_name);

        $this->socketName = $socket_name;

        isset($info['scheme']) && $this->scheme = $info['scheme'];
        isset($info['host']) && $this->host = $info['host'];
        isset($info['port']) && $this->port = $info['port'];
        isset($info['path']) && $this->endpoint = $info['path'];
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
