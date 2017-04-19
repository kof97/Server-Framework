<?php

namespace Framework\Coroutine;

use Framework\Coroutine\Scheduler;

/**
 * Class IoPoll.
 * IO Poll.
 *
 * @category PHP
 */
class IoPoll
{
	/**
	 * @var Scheduler The obj of Scheduler.
	 */
	protected $schedule;

	/**
	 * @var array IO queue that waiting to read [resourceID => [socket, tasks]].
	 */
	protected $waitingForRead = array();

	/**
	 * @var array IO queue that waiting to write [resourceID => [socket, tasks]].
	 */
	protected $waitingForWrite = array();

	/**
	 * Init Scheduler.
	 *
	 * @param Scheduler $schedule The obj of Scheduler.
	 */
	public function __construct(Scheduler $schedule)
	{
		$this->schedule = $schedule;
	}

	/**
	 * IO queue that waiting to read.
	 * It can be used by SystemCall.
	 *
	 * @param Resource $socket IO stream.
	 * @param Task     $task   The task that from the resource.
	 */
	public function waitForRead($socket, Task $task)
	{
		if (isset($this->waitingForRead[(int) $socket])) {
			$this->waitingForRead[(int) $socket][1][] = $task;
		} else {
			$this->waitingForRead[(int) $socket] = array($socket, [$task]);
		}
	}

	/**
	 * IO queue that waiting to write.
	 * It can be used by SystemCall.
	 *
	 * @param Resource $socket IO stream.
	 * @param Task     $task   The task that from the resource.
	 */
	public function waitForWrite($socket, Task $task)
	{
		if (isset($this->waitingForWrite[(int) $socket])) {
			$this->waitingForWrite[(int) $socket][1][] = $task;
		} else {
			$this->waitingForWrite[(int) $socket] = array($socket, [$task]);
		}
	}

	/**
	 * IO poll.
	 * Get the tasks form the IO waiting/writing queue.
	 * Add the socket tasks into the schedule.
	 * Or block indefinitely until some new streams occurs.
	 *
	 * @param int|null $timeout 0 for not wait, and null for block until an event on the watched streams occurs.
	 */
	protected function poll($timeout)
	{
		$rSocks = [];
		foreach ($this->waitingForRead as list($socket)) {
			$rSocks[] = $socket;
		}

		$wSocks = [];
		foreach ($this->waitingForWrite as list($socket)) {
			$wSocks[] = $socket;
		}

		$eSocks = [];

		$res = @stream_select($rSocks, $wSocks, $eSocks, $timeout);
		if (!$res) {
			return;
		}

		foreach ($rSocks as $socket) {
			list(, $tasks) = $this->waitingForRead[(int) $socket];
			unset($this->waitingForRead[(int) $socket]);

			foreach ($tasks as $task) {
				$this->schedule->schedule($task);
			}
		}

		foreach ($wSocks as $socket) {
			list(, $tasks) = $this->waitingForWrite[(int) $socket];
			unset($this->waitingForWrite[(int) $socket]);

			foreach ($tasks as $task) {
				$this->schedule->schedule($task);
			}
		}
	}

	/**
	 * Set the IO poll status.
	 * 0 for not wait.
	 * Null for block until an event on the watched streams occurs.
	 *
	 * @return Generator
	 */
	public function ioPollTask()
	{
		while (true) {
			if ($this->schedule->getTaskQueue()->isEmpty()) {
				$this->poll(null);
			} else {
				$this->poll(0);
			}

			yield;
		}
	}
}

// end of script
