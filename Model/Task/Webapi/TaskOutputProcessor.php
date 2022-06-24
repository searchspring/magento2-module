<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Task\Webapi;

use SearchSpring\Feed\Api\Data\TaskInterface;

class TaskOutputProcessor
{
    /**
     * @param TaskInterface $task
     * @param array $outputData
     * @return array
     */
    public function execute(TaskInterface $task, array $outputData) : array
    {
        $payload = $task->getPayload();
        $outputData[TaskInterface::PAYLOAD] = $payload;
        return $outputData;
    }
}
