<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Task\GenerateFeed;

use SearchSpring\Feed\Api\Data\TaskInterface;
use SearchSpring\Feed\Model\Task\ExecutorInterface;

class Executor implements ExecutorInterface
{

    /**
     * @param TaskInterface $task
     * @return mixed
     */
    public function execute(TaskInterface $task)
    {
        // TODO: Implement execute() method.
    }
}
