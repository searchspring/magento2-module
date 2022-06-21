<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Api;

use SearchSpring\Feed\Api\Data\TaskInterface;

interface ExecuteTaskInterface
{
    /**
     * @param TaskInterface $task
     * @return mixed
     */
    public function execute(TaskInterface $task);
}
