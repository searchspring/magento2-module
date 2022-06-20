<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Api;

use SearchSpring\Feed\Api\Data\TaskInterface;

interface CreateTaskInterface
{
    /**
     * @param string $type
     * @param array $payload
     * @return TaskInterface
     */
    public function execute(string $type, array $payload) : TaskInterface;
}
