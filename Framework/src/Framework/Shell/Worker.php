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

        $msg = 'Receive from ';

        var_dump($this->getRequestHeaders());

        fwrite($conn, 'From ' . $this->host . ': ' . PHP_EOL);
    }

    protected function getRequestHeaders()
    {
        if (function_exists('getallheaders')) {
            return getallheaders();
        }

        $headers = array();
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        if (isset($_SERVER['PHP_AUTH_DIGEST'])) {
            $header['AUTHORIZATION'] = $_SERVER['PHP_AUTH_DIGEST']);
        } elseif (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
            $header['AUTHORIZATION'] = base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $_SERVER['PHP_AUTH_PW']));
        }

        if (isset($_SERVER['CONTENT_LENGTH'])) {
            $header['CONTENT-LENGTH'] = $_SERVER['CONTENT_LENGTH'];
        }

        if (isset($_SERVER['CONTENT_TYPE'])) {
            $header['CONTENT-TYPE'] = $_SERVER['CONTENT_TYPE'];
        }

        return $headers;
    }
}

// end of script
