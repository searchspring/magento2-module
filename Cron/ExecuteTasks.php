<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Cron;

use SearchSpring\Feed\Api\ExecutePendingTasksInterface;

class ExecuteTasks
{
    /**
     * @var ExecutePendingTasksInterface
     */
    private $executePendingTasks;

    /**
     * ExecuteTasks constructor.
     * @param ExecutePendingTasksInterface $executePendingTasks
     */
    public function __construct(
        ExecutePendingTasksInterface $executePendingTasks
    ) {
        $this->executePendingTasks = $executePendingTasks;
    }

    /**
     *
     */
    public function execute() : void
    {
        $this->executePendingTasks->execute();
    }
}
