<?php

namespace Framework\Shell;

use Framework\Event\EventInterface;

/**
 * Class Worker.
 *
 * @category PHP
 */
class Worker
{
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


        $this->listen();
    }

    protected function listen()
    {
        if (Master::$globalEvent) {
            if ($this->scheme === 'udp') {
                Master::$globalEvent->add(Master::$socket, EventInterface::EV_READ, array($this, 'acceptUdpConnection'));
            } else {
                Master::$globalEvent->add(Master::$socket, EventInterface::EV_READ, array($this, 'acceptConnection'));
            }
        }

        Master::$globalEvent->loop();
    }

    public function acceptConnection($socket)
    {
        $conn = @stream_socket_accept($socket, 0, $remote_address);

        if (!$conn) {
            return;
        }

        var_dump(123);

        fwrite($conn, 'string' . PHP_EOL);
    }
}

// end of script
