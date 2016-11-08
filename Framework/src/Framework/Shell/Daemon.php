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
        $this->checkSystem();

        $this->pidfile = '../run/FrameServer.pid';
        $this->setProcessTitle('FrameServer');

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

            posix_kill($pid, 9);
        } else {
            echo 'Not found the server ! !' . PHP_EOL;
        }
    }

    private function reload()
    {
        if (!is_file($this->pidfile)) {
            exit('Please start the server first' . PHP_EOL);
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
            exit('Please input params' . PHP_EOL);
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

    private function checkSystem()
    {
        if (strpos(PHP_SAPI, 'cli') === false) {
            exit('Only run in command line mode ! !' . PHP_EOL);
        }

        if (!function_exists('pcntl_fork')) {
            exit('Your system can\'t support portable operating system interface of Unix ! !' . PHP_EOL);
        }
    }

    private function setProcessTitle($title)
    {
        // >= php 5.5
        if (function_exists('cli_set_process_title')) {
            @cli_set_process_title($title);
        } elseif (extension_loaded('proctitle') && function_exists('setproctitle')) {
            @setproctitle($title);
        }
    }

    private function log($msg)
    {
        $msg = $msg . "\n";
        if (!self::$daemonize) {
            echo $msg;
        }
        file_put_contents(self::$logFile, date('Y-m-d H:i:s') . ' ' . 'pid:'. posix_getpid() . ' ' . $msg, FILE_APPEND | LOCK_EX);
    }
}

// end of script
