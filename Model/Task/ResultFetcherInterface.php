<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Task;

use SearchSpring\Feed\Api\Data\TaskInterface;

interface ResultFetcherInterface
{
    /**
     * @param TaskInterface $task
     * @return mixed
     */
    public function fetch(TaskInterface $task);
}
