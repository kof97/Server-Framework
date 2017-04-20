<?php

namespace Server\Coroutine;

use Server\Coroutine\IoPoll;
use \SplQueue;
use \Generator;
use \Exception;

/**
 * Class Scheduler.
 * Coroutine tasks' scheduler.
 *
 * @category PHP
 */
class Scheduler
{
	/**
	 * @var int Task id.
	 */
	protected $maxTaskId = 0;

	/**
	 * @var array [TaskId => Task] 任务映射 map.
	 */
	protected $taskMap = [];

	/**
	 * @var SplQueue Task queue.
	 */
	protected $taskQueue;

	/**
	 * @var IoPoll Poll.
	 */
	protected $poll;

	/**
	 * Init task queue.
	 */
	public function __construct()
	{
		$this->taskQueue = new SplQueue();
		$this->poll = new IoPoll($this);
	}

	/**
	 * New task.
	 *
	 * @param Generator $coroutine 生成器函数.
	 * @return int Task id.
	 */
	public function newTask(Generator $coroutine)
	{
		$tid = ++$this->maxTaskId;
		$task = new Task($tid, $coroutine);
		$this->taskMap[$tid] = $task;
		$this->schedule($task);

		return $tid;
	}

	/**
	 * Schedule the task into the tasksQueue.
	 *
	 * @param Task $task
	 */
	public function schedule(Task $task)
	{
		$this->taskQueue->enqueue($task);
	}

	/**
	 * Run the tasks that from the SplQueue.
	 */
	public function run()
	{
		// $this->newTask($this->poll->ioPollTask());

		while (!$this->taskQueue->isEmpty()) {
			$task = $this->taskQueue->dequeue();
			$retval = $task->run();

			if ($retval instanceof SystemCall) {
				try {
					$retval($task, $this);
				} catch (Exception $e) {
					$task->setException($e);
					$this->schedule($task);
				}

				continue;
			}

			if ($task->isFinished()) {
				unset($this->taskMap[$task->getTaskId()]);
			} else {
				$this->schedule($task);
			}
		}
	}

	/**
	 * Kill the task.
	 * It can be used by SystemCall.
	 *
	 * @param int $tid The task id.
	 * @return bool
	 */
	public function killTask($tid)
	{
		if (!isset($this->taskMap[$tid])) {
			return false;
		}

		unset($this->taskMap[$tid]);

		// This is a bit ugly and could be optimized so it does not have to walk the queue,
		// but assuming that killing tasks is rather rare I won't bother with it now
		foreach ($this->taskQueue as $i => $task) {
			if ($task->getTaskId() === $tid) {
				unset($this->taskQueue[$i]);
				break;
			}
		}

		return true;
	}

	/**
	 * Get the task queue.
	 *
	 * @return array
	 */
	public function getTaskQueue()
	{
		return $this->taskQueue;
	}
}

// end of script
