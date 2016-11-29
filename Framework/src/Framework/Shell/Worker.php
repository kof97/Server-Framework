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

    protected $context = null;

    public function __construct($socket_name = '')
    {
        $info = parse_url($socket_name);

        $this->socketName = $socket_name;

        isset($info['scheme']) && $this->scheme = $info['scheme'];
        isset($info['host']) && $this->host = $info['host'];
        isset($info['port']) && $this->port = $info['port'];
        isset($info['path']) && $this->endpoint = $info['path'];

        if ($socket_name !== '') {
            $context_option['socket']['backlog'] = 1024;
            $this->context = stream_context_create($context_option);
        }
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

        $flags  = $this->scheme === 'udp' ? STREAM_SERVER_BIND : STREAM_SERVER_BIND | STREAM_SERVER_LISTEN;
        $errno  = 0;
        $errmsg = '';

        $this->socket = stream_socket_server($this->socketName, $errno, $errmsg, $flags, $this->context);

        // Try to open keepalive for tcp and disable Nagle algorithm.
        if (function_exists('socket_import_stream') && $this->scheme === 'tcp') {
            $socket = socket_import_stream($this->socket);
            @socket_set_option($socket, SOL_SOCKET, SO_KEEPALIVE, 1);
            @socket_set_option($socket, SOL_TCP, TCP_NODELAY, 1);
        }

        stream_set_blocking($this->socket, 0);

        if (Master::$globalEvent) {
            if ($this->scheme === 'udp') {
                Master::$globalEvent->add($this->socket, EventInterface::EV_READ, array($this, 'acceptUdpConnection'));
            } else {
                Master::$globalEvent->add($this->socket, EventInterface::EV_READ, array($this, 'acceptConnection'));
            }
        }
    }
}

// end of script
