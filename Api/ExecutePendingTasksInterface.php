<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Api;

interface ExecutePendingTasksInterface
{
    /**
     * @return array
     */
    public function execute() : array;
}
