<?php

namespace Framework\Shell;

use Framework\Shell\Signal;

/**
 * Class Master.
 *
 * @category PHP
 */
class Master
{
    const SERVER_NAME = 'FrameServer';

    /**
     * @var ['worker_pid' => 'filename']
     */
    protected $count = 4;

    protected $runTimeRoot = '../run/';

    protected $masterPidFile = null;

    /**
     * @var ['worker_pid' => 'filename']
     */
    protected $pidFileList = array();

    /**
     * @var ['worker_pid' => obj]
     */
    protected $workers = array();

    protected $isMaster = true;

    protected $daemonize = false;

    public function __construct()
    {
        $this->checkSystem();

        $this->masterPidFile = $this->runTimeRoot . self::SERVER_NAME . '.pid';
    }

    protected function signal()
    {
        pcntl_signal(SIGHUP, function ($signo) {
            echo 'The server has been reload' . PHP_EOL;
            Signal::set($signo);
        });
    }

    protected function daemon()
    {
        if (is_file($this->masterPidFile)) {
            echo "The file $this->masterPidFile exists" . PHP_EOL;
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
            file_put_contents($this->masterPidFile, getmypid());
            $this->setProcessTitle('Master: ' . self::SERVER_NAME);

            return getmypid();
        }
    }

    protected function run()
    {
        $this->signal();
        $this->loadConf();

        while (true) {
            if ($this->isMaster === false) {
                exit;
            }

            $this->checkWorkers();

            pcntl_signal_dispatch();


            sleep(2);

            if (Signal::get() == SIGHUP) {
                Signal::reset();
                break;
            }
        }
    }

    protected function loadConf()
    {

    }

    protected function initWorkers()
    {
        
    }

    protected function checkWorkers()
    {
        while (count($this->workers) < $this->count) {
            $this->forkWorker();
        }
    }

    protected function forkWorker()
    {
        $pid = pcntl_fork();

        $worker = new Worker();

        if ($pid < 0) {
            exit('Fork fail');
        } else if ($pid > 0) {
            $this->workers[$pid] = $worker;
            $this->pidFileList[$pid] = $this->runTimeRoot . 'Worker_' . $pid . '.pid';
            file_put_contents($this->pidFileList[$pid], $pid);
        } else {
            $this->setProcessTitle('Worker: ' . self::SERVER_NAME);
            $this->isMaster = false;
            $this->count = 0;

            $worker->run();
        }
    }

    protected function start()
    {
        $pid = $this->daemon();

        while (true) {
            sleep(1);

            $this->run();
        }
    }

    protected function stop()
    {
        exec("ps aux | grep run.php | awk '{print $2}'", $output);
        $total = 0;

        foreach ($output as $value) {
            $pid_file = $this->runTimeRoot . 'Worker_' . $value . '.pid';
            if (is_file($pid_file)) {
                $pid = file_get_contents($pid_file);
                unlink($pid_file);

                $total++;
                echo str_pad('- Kill the process Worker_' . $value, 35, ' ') . "\033[32m [success] \033[0m" . PHP_EOL;

                posix_kill($pid, 9);
            }
        }

        if ($total > 0) {
            echo PHP_EOL . 'Total kill ' . $total . ' worker process' . PHP_EOL . PHP_EOL;
        }

        if (is_file($this->masterPidFile)) {
            $pid = file_get_contents($this->masterPidFile);
            unlink($this->masterPidFile);

            echo str_pad('* Kill the master process', 30, ' ') . "\033[32m [success] \033[0m" . PHP_EOL . PHP_EOL;
            echo '* Done' . PHP_EOL . PHP_EOL;

            posix_kill($pid, 9);
        } else {
            echo 'Not found the server ! !' . PHP_EOL;
        }
    }

    protected function reload()
    {
        if (!is_file($this->masterPidFile)) {
            exit('Please start the server first' . PHP_EOL);
        }

        $pid = file_get_contents($this->masterPidFile);
        posix_kill($pid, SIGHUP);
    }

    protected function restart()
    {
        if (is_file($this->masterPidFile)) {
            $this->stop();
        }

        $this->start();
    }

    protected function status()
    {
        if (!is_file($this->masterPidFile)) {
            exit('Server is not running' . PHP_EOL);
        }

        $master_pid = file_get_contents($this->masterPidFile);

        echo 'Server ' . self::SERVER_NAME . 'is running' . PHP_EOL;
        echo '* PHP version: ' . PHP_VERSION . PHP_EOL . PHP_EOL;
        echo str_pad('* Master Process ID: ' . $master_pid, 30, ' ') . "\033[32m [running] \033[0m" . PHP_EOL . PHP_EOL;

        exec("ps aux | grep run.php | awk '{print $2}'", $output);
        $total = 0;

        foreach ($output as $pid) {
            if (is_file($this->runTimeRoot . 'Worker_' . $pid . '.pid')) {
                echo str_pad('- Worker Process ID: ' . $pid, 30, ' ') . "\033[32m [running] \033[0m" . PHP_EOL;

                $total++;
            }
        }

        echo PHP_EOL . '* Total ' . $total . ' worker process' . PHP_EOL . PHP_EOL;
    }

    protected function help()
    {
        echo 'Usage:' . PHP_EOL;
        echo '- start | stop | restart | reload | status | help' . PHP_EOL;
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

            case 'status':
                $this->status();
                break;

            case 'help':
                $this->help();
                break;

            default:
                break;
        }
    }

    protected function checkSystem()
    {
        if (strpos(PHP_SAPI, 'cli') === false) {
            exit('Only run in command line mode ! !' . PHP_EOL);
        }

        if (!function_exists('pcntl_fork')) {
            exit('Your system can\'t support portable operating system interface of Unix ! !' . PHP_EOL);
        }
    }

    protected function setProcessTitle($title)
    {
        // >= php 5.5
        if (function_exists('cli_set_process_title')) {
            @cli_set_process_title($title);
        } elseif (extension_loaded('proctitle') && function_exists('setproctitle')) {
            @setproctitle($title);
        }
    }

    protected function log($msg)
    {
        $msg = $msg . "\n";
        if (!self::$daemonize) {
            echo $msg;
        }

        file_put_contents(self::$logFile, date('Y-m-d H:i:s') . ' ' . 'pid:'. posix_getpid() . ' ' . $msg, FILE_APPEND | LOCK_EX);
    }
}

// end of script
