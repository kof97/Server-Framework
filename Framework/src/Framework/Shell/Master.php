<?php

namespace Framework\Shell;

use Framework\Shell\Signal;
use Framework\Event\EventInterface;

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

    public static $socket = null;

    protected $port = 8899;

    protected $ip = '0.0.0.0';

    protected $protocol = 'tcp';

    protected $socketName = '';

    protected $context = null;

    protected $protocolMap = array(
        'http'  => 'tcp',
        'tcp'   => 'tcp',
        'udp'   => 'udp',
        'ssl'   => 'tcp',
        'sslv2' => 'tcp',
        'sslv3' => 'tcp',
        'tls'   => 'tcp',
        'unix'  => 'unix'
    );

    protected $eventLoops = array(
        'libevent',
    );

    protected $eventClassName = null;

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

    /**
     * Init socket, check system and install signal.
     */
    public function __construct()
    {
        $this->checkSystem();
        $this->signal();

        $this->socketName = $this->protocol . '://' . $this->ip . ':' . $this->port;

        if ($this->socketName !== '') {
            $context_option['socket']['backlog'] = 1024;
            $this->context = stream_context_create($context_option);
        }

        $this->masterPidFile = self::RUN_TIME . self::SERVER_NAME . '.pid';
    }

    protected function signal()
    {
        pcntl_signal(SIGHUP, function ($signo) {
            echo 'The server is reloaded' . PHP_EOL;
            Signal::set($signo);
        }, false);

        pcntl_signal(SIGINT, function ($signo) {
            Signal::set($signo);
        }, false);
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
        $this->prepareEventClass();

        if (self::$socket === null) {
            $this->initSocket($this->socketName, $this->context);
        }

        while (true) {
            $this->checkWorkers();

            pcntl_signal_dispatch();

            if (Signal::get() == SIGHUP) {
                Signal::reset();
                break;
            }

            if (Signal::get() == SIGINT) {
                $this->closeSocket();
                break;
            }

            sleep(2);
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

    protected function checkWorkers()
    {
        // clear abnormal worker
        exec("ps aux | grep run.php | awk '{print $2}'", $output);
        if ($handle = opendir(self::RUN_TIME)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && strpos($file, self::SERVER_NAME) === false) {
                    $point_pid = substr($file, strpos($file, '_') + 1);
                    $pid = substr($point_pid, 0, strlen($point_pid) - 4);

                    if (!in_array($pid, $output)) {
                        if (isset($this->workers[$pid])) {
                            unset($this->workers[$pid]);
                        }
                        if (isset($this->pidFileList[$pid])) {
                            unset($this->pidFileList[$pid]);
                        }

                        @unlink(self::RUN_TIME . 'Worker_' . $pid . '.pid');
                    }
                }
            }

            closedir($handle);
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
        $worker = new Worker($this->socketName);
        $worker->initEventClass($this->eventClassName);

        $pid = pcntl_fork();

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

    protected function prepareEventClass()
    {
        if (!$this->eventClassName) {
            $event_class = '\\Framework\\Event\\' . ucfirst($this->getEventLoopName());

            if (!$this->getEventLoopName()) {
                exit('no extension');
            }

            $this->eventClassName = $event_class;
        }
    }

    protected function initSocket($socket_name = '', $context = array())
    {
        $flags  = $this->protocol === 'udp' ? STREAM_SERVER_BIND : STREAM_SERVER_BIND | STREAM_SERVER_LISTEN;
        $errno  = 0;
        $errmsg = '';

        self::$socket = stream_socket_server($socket_name, $errno, $errmsg, $flags, $context);

        // Try to open keepalive for tcp and disable Nagle algorithm.
        if (function_exists('socket_import_stream') && $this->protocol === 'tcp') {
            $socket = socket_import_stream(self::$socket);
            @socket_set_option($socket, SOL_SOCKET, SO_KEEPALIVE, 1);
            @socket_set_option($socket, SOL_TCP, TCP_NODELAY, 1);
        }

        stream_set_blocking(self::$socket, 0);
    }

    protected function start()
    {
        $this->daemon();

        while (true) {
            sleep(1);

            $this->run();
        }
    }

    protected function closeSocket()
    {
        // $this->eventClassName->del(self::$socket, EventInterface::EV_READ);
        @fclose(self::$socket);

        echo str_pad('* Close the socket', 25, ' ') . "\033[32m [OK] \033[0m" . PHP_EOL . PHP_EOL;
    }

    protected function stop()
    {
        if (!is_file($this->masterPidFile)) {
            exit('Not found the server ! !' . PHP_EOL);
        }

        $master_pid = file_get_contents($this->masterPidFile);

        posix_kill($master_pid, SIGINT);

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

        echo str_pad('* Kill the master process', 30, ' ') . "\033[32m [success] \033[0m" . PHP_EOL . PHP_EOL;
        echo '* Done' . PHP_EOL . PHP_EOL;

        unlink($this->masterPidFile);
        posix_kill($master_pid, 9);
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
        $this->checkInit();

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

    protected function checkInit()
    {
        is_dir('../run/') || exit('Not found the dir "run", Please run init.php first' . PHP_EOL);
        is_dir('../log/') || exit('Not found the dir "log", Please run init.php first' . PHP_EOL);
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

    /**
     * @deprecated
     */
    protected function getLibevent()
    {
        if (extension_loaded('libevent')) {
            return 'libevent';
        }

        exit('Don\'t have the extension libevent');
    }

    protected function getEventLoopName()
    {
        foreach ($this->eventLoops as $name) {
            if (extension_loaded($name)) {
                $event_loop_name = $name;
                break;
            }
        }

        return $event_loop_name;
    }

    protected function log($msg = '')
    {
        return true;

        $msg = $msg . "\n";
        if (!self::$daemonize) {
            echo $msg;
        }

        file_put_contents(self::$logFile, date('Y-m-d H:i:s') . ' ' . 'pid:'. posix_getpid() . ' ' . $msg, FILE_APPEND | LOCK_EX);
    }
}

// end of script
