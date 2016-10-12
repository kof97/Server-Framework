<?php

namespace Framework\Coroutine;

use \Generator;
use \SplStack;

/**
 * Class Task.
 * Coroutine task.
 *
 * @category PHP
 * @package  Coroutine
 * @author   Arno [<arnoliu@tencent.com> | <1048434786@qq.com>]
 */
class Task
{
    /**
     * @var Task id.
     */
    protected $taskId;

    /**
     * @var Coroutine task stack.
     */
    protected $coroutine;

    /**
     * @var The send value.
     */
    protected $sendValue = null;

    /**
     * @var Wether it is the first yield.
     */
    protected $beforeFirstYield = true;

    /**
     * @var Error message.
     */
    protected $exception = null;

    /**
     * Init the Task and set it into stack.
     *
     * @param int       $taskId    The task id.
     * @param Generator $coroutine The coroutine task.
     */
    public function __construct($taskId, Generator $coroutine)
    {
        $this->taskId = $taskId;
        $this->coroutine = $this->StackedCoroutine($coroutine);
    }

    /**
     * Get the task id.
     *
     * @return int Task id.
     */
    public function getTaskId()
    {
        return $this->taskId;
    }

    /**
     * Set the send value.
     *
     * @param mixed $sendValue The next yield value.
     */
    public function setSendValue($sendValue)
    {
        $this->sendValue = $sendValue;
    }

    /**
     * Set the error massage.
     *
     * @param string $exception Error massage.
     */
    public function setException($exception)
    {
        $this->exception = $exception;
    }

    /**
     * Set the Generator into the stack.
     * Use the stack is to keep the Generator run environment
     *
     * @param Generator $gen The Generator task.
     */
    public function stackedCoroutine(Generator $gen)
    {
        $stack = new SplStack;
        $exception = null;

        while (true) {
            try {
                if ($exception) {
                    $gen->throw($exception);
                    $exception = null;
                    continue;
                }

                $value = $gen->current();

                if ($value instanceof Generator) {
                    $stack->push($gen);
                    $gen = $value;
                    continue;
                }

                $isReturnValue = $value instanceof ReturnValue;
                if (!$gen->valid() || $isReturnValue) {
                    if ($stack->isEmpty()) {
                        return;
                    }

                    $gen = $stack->pop();
                    $gen->send($isReturnValue ? $value->getValue() : NULL);
                    continue;
                }

                try {
                    $sendValue = (yield $gen->key() => $value);
                } catch (Exception $e) {
                    $gen->throw($e);
                    continue;
                }

                $gen->send($sendValue);
            } catch (Exception $e) {
                if ($stack->isEmpty()) {
                    throw $e;
                }

                $gen = $stack->pop();
                $exception = $e;
            }
        }
    }

    /**
     * Run the task.
     *
     * @return mixed
     */
    public function run()
    {
        if ($this->beforeFirstYield) {
            $this->beforeFirstYield = false;
            return $this->coroutine->current();
        } elseif ($this->exception) {
            $retval = $this->coroutine->throw($this->exception);
            $this->exception = null;
            return $retval;
        } else {
            $retval = $this->coroutine->send($this->sendValue);
            $this->sendValue = null;
            return $retval;
        }
    }

    /**
     * Wether the coroutine is finished.
     *
     * @return bool
     */
    public function isFinished()
    {
        return !$this->coroutine->valid();
    }
}

// end of script
