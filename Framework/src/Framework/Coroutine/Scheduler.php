<?php

namespace Framework\Coroutine;

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
     * @var array IO queue that waiting to read [resourceID => [socket, tasks]].
     */
    protected $waitingForRead = array();

    /**
     * @var array IO queue that waiting to write [resourceID => [socket, tasks]].
     */
    protected $waitingForWrite = array();


    /**
     * Init task queue.
     */
    public function __construct()
    {
        $this->taskQueue = new SplQueue();
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
        $this->newTask($this->ioPollTask());

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
    protected function ioPoll($timeout)
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
                $this->schedule($task);
            }
        }

        foreach ($wSocks as $socket) {
            list(, $tasks) = $this->waitingForWrite[(int) $socket];
            unset($this->waitingForWrite[(int) $socket]);

            foreach ($tasks as $task) {
                $this->schedule($task);
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
    protected function ioPollTask()
    {
        while (true) {
            if ($this->taskQueue->isEmpty()) {
                $this->ioPoll(null);
            } else {
                $this->ioPoll(0);
            }

            yield;
        }
    }
}

// end of script
