<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Task;

use SearchSpring\Feed\Api\Data\TaskInterface;

interface ExecutorInterface
{
    /**
     * @param TaskInterface $task
     * @return mixed
     */
    public function execute(TaskInterface $task);
}
