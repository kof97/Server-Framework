<?php

namespace Framework\Coroutine;

/**
 * Class SystemCall.
 * The SystemCall, some method can be used by functions such as the system call.
 *
 * @category PHP
 */
class SystemCall
{
    /**
     * @var The callback.
     */
    protected $callback;

    /**
     * Init the callback.
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * It can be used by funcitons.
     *
     * @param Task      $task      The task.
     * @param Scheduler $scheduler The task scheduler.
     */
    public function __invoke(Task $task, Scheduler $scheduler)
    {
        $callback = $this->callback;
        return $callback($task, $scheduler);
    }
}

// end of script
