<?php

class TestPosix {
	const LISTEN = 'tcp://localhost:9999';

	protected $pidfile;

	public function __construct() {
		$this->pidfile = './run/' . __CLASS__ . '.pid';
	}

	private function daemon() {
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

	private function start() {
		$pid = $this->daemon();
	}

	private function stop() {
		if (is_file($this->pidfile)) {
			$pid = file_get_contents($this->pidfile);
			unlink($this->pidfile);

			posix_kill($pid, 9);
		} else {
			echo 'Not found the server ! !' . PHP_EOL;
		}

		exit;
	}

	private function help() {
		echo 'Usage:' . PHP_EOL;
		echo '- start | stop | help' . PHP_EOL;
		exit;
	}

	public function init($argc, $argv) {
		if ($argc < 2) {
			echo 'Please input help' . PHP_EOL;
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

			case 'help':
				$this->help();
				break;

			default:
				break;
		}
	}
}

$test = new TestPosix();

$test->init($argc, $argv);

// end of script
