<?php

namespace Framework\Shell;

use Framework\Shell\Signal;

/**
 * Class Daemon.
 *
 * @category PHP
 */
class Daemon
{
    protected $pidfile;

    public function __construct()
    {
        $this->pidfile = '../run/FrameServer.pid';

        $this->signal();
    }

    private function signal()
    {
        pcntl_signal(SIGHUP, function ($signo) {
            echo 'The server has been reload' . PHP_EOL;
            Signal::set($signo);
        });
    }

    private function daemon()
    {
        if (is_file($this->pidfile)) {
            echo "The file $this->pidfile exists" . PHP_EOL;
            echo 'It has already started ! !' . PHP_EOL;
            exit;
        }

        $pid = pcntl_fork();
        if ($pid == -1) {
            die('Could not fork');
        } else if ($pid) {
            // pcntl_wait($status);
            exit;
        } else {
            file_put_contents($this->pidfile, getmypid());

            return getmypid();
        }
    }

    private function run()
    {
        while (true) {
            pcntl_signal_dispatch();

            sleep(2);

            if (Signal::get() == SIGHUP) {
                Signal::reset();
                break;
            }
        }
    }

    private function start()
    {
        $pid = $this->daemon();

        echo 'Start the server' . PHP_EOL;

        while (true) {
            sleep(1);

            $this->run();
        }
    }

    private function stop()
    {
        if (is_file($this->pidfile)) {
            $pid = file_get_contents($this->pidfile);
            unlink($this->pidfile);

            echo 'Stop the server' . PHP_EOL;

            posix_kill($pid, 9);
        } else {
            echo 'Not found the server ! !' . PHP_EOL;
        }
    }

    private function reload()
    {
        if (!is_file($this->pidfile)) {
            echo 'Please start the server first' . PHP_EOL;
            exit;
        }

        $pid = file_get_contents($this->pidfile);
        posix_kill($pid, SIGHUP);
    }

    private function restart()
    {
        if (is_file($this->pidfile)) {
            $this->stop();
        }

        $this->start();
    }

    private function help()
    {
        echo 'Usage:' . PHP_EOL;
        echo '- start | stop | restart | reload | help' . PHP_EOL;
        exit;
    }

    public function init($argc, $argv)
    {
        if ($argc < 2) {
            echo 'Please input params' . PHP_EOL;
            exit;
        }

        $cmd = $argv[1];
        switch ($cmd) {
            case 'start':
                $this->start();
                break;

            case 'stop':
                $this->stop();
                break;

            case 'reload':
                $this->reload();
                break;

            case 'restart':
                $this->restart();
                break;

            case 'help':
                $this->help();
                break;

            default:
                break;
        }
    }
}

// end of script
