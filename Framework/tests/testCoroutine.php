<?php

require dirname(__DIR__) . 'lib' . DIRECTORY_SEPARATOR . 'AutoLoader.php';

use Framework\Coroutine\Scheduler;
use Framework\Coroutine\SystemCall;
use Framework\Coroutine\Task;

function getTaskId() {
    return new SystemCall(
        function(Task $task, Scheduler $scheduler) {

            $task->setSendValue($task->getTaskId());
            $scheduler->schedule($task);
        }
    );
}

function newTask(Generator $coroutine) {
    return new SystemCall(
        function(Task $task, Scheduler $scheduler) use ($coroutine) {
            $task->setSendValue($scheduler->newTask($coroutine));
            $scheduler->schedule($task);
        }
    );
}

function killTask($tid) {
    return new SystemCall(
        function(Task $task, Scheduler $scheduler) use ($tid) {
            if ($scheduler->killTask($tid)) {
                $scheduler->schedule($task);
            } else {
                throw new InvalidArgumentException('Invalid task ID!');
            }
        }
    );
}



function childTask() {
    $tid = (yield getTaskId());
    while (true) {
        echo "Child task $tid still alive!\n";
        yield;
    }
}

function task() {
    $tid = (yield getTaskId());
    $childTid = (yield newTask(childTask()));

    for ($i = 1; $i <= 6; ++$i) {
        echo "Parent task $tid iteration $i.\n";
        yield;
 
        if ($i == 3) {
            yield killTask($childTid);
        }


    }
}

$scheduler = new Scheduler;
$scheduler->newTask(task());
$scheduler->run();

// end of script
