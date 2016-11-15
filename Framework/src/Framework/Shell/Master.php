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

    const RUN_TIME = '../run/';

    const CONF_PATH = '../conf/server.ini';

    /**
     * @var worker num
     */
    protected $count = 0;

    protected $masterPidFile = null;

    /**
     * @var ['worker_pid' => 'filename']
     */
    protected $pidFileList = array();

    /**
     * @var ['worker_pid' => obj]
     */
    protected $workers = array();

    protected $pid = null;

    protected $isMaster = true;

    protected $daemonize = false;

    public function __construct()
    {
        $this->checkSystem();
        $this->signal();

        $this->masterPidFile = self::RUN_TIME . self::SERVER_NAME . '.pid';
    }

    protected function signal()
    {
        pcntl_signal(SIGHUP, function ($signo) {
            echo 'The server will be reloaded after 1s' . PHP_EOL;
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

            $this->pid = getmypid();
        }
    }

    protected function run()
    {
        $this->loadConf();

        while (true) {
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
        if ($this->isMaster === false) {
            // todo, something wrong
            return;
        }

        if (!is_file(self::CONF_PATH)) {
            exit('Not found the config' . PHP_EOL);
        }

        $ini = parse_ini_file(self::CONF_PATH, true);

        $this->count = $ini['base']['worker'];
    }

    protected function initWorkers()
    {
        
    }

    protected function checkWorkers()
    {
        $unset_list = array();
        foreach ($this->pidFileList as $pid => $file) {
            if (!is_file($file)) {
                array_push($unset_list, $pid);
            }
        }

        foreach ($unset_list as $pid) {
            if (isset($this->workers[$pid])) {
                unset($this->workers[$pid]);
            }

            if (isset($this->pidFileList[$pid])) {
                unset($this->pidFileList[$pid]);
            }
        }

        while (count($this->workers) < $this->count) {
            $this->forkWorker();
        }

        if ($this->isMaster === false) {
            $pid_file = self::RUN_TIME . 'Worker_' . $this->pid . '.pid';
            if (is_file($pid_file)) {
                unlink($pid_file);
            }

            posix_kill($this->pid, 9);
            exit;
        }

        $shutdown_num = count($this->workers) - $this->count;
        if ($shutdown_num > 0) {
            $this->shutdownWorker($shutdown_num);
        }
    }

    protected function forkWorker()
    {
        $pid = pcntl_fork();

        $worker = new Worker();

        if ($pid < 0) {
            // todo log
            exit('Fork fail');
        } else if ($pid > 0) {
            $this->workers[$pid] = 1;
            $this->pidFileList[$pid] = self::RUN_TIME . 'Worker_' . $pid . '.pid';
            file_put_contents($this->pidFileList[$pid], $pid);
        } else {
            $this->setProcessTitle('Worker: ' . self::SERVER_NAME);
            $this->isMaster = false;
            $this->count = 0;
            $this->pid = posix_getpid();

            $worker->run();
        }
    }

    protected function shutdownWorker($num)
    {
        // todo
    }

    protected function start()
    {
        $this->daemon();

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
            $pid_file = self::RUN_TIME . 'Worker_' . $value . '.pid';
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

        echo 'Server ' . self::SERVER_NAME . ' is running' . PHP_EOL;
        echo '* PHP version: ' . PHP_VERSION . PHP_EOL . PHP_EOL;
        echo str_pad('* Master Process ID: ' . $master_pid, 30, ' ') . "\033[32m [running] \033[0m" . PHP_EOL . PHP_EOL;

        exec("ps aux | grep run.php | awk '{print $2}'", $output);
        $total = 0;

        foreach ($output as $pid) {
            if (is_file(self::RUN_TIME . 'Worker_' . $pid . '.pid')) {
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
            $this->help();
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
