<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Task\Webapi;

use SearchSpring\Feed\Api\Data\TaskResultInterface;

class TaskResultOutputProcessor
{
    /**
     * @param TaskResultInterface $task
     * @param array $outputData
     * @return array
     */
    public function execute(TaskResultInterface $task, array $outputData) : array
    {
        $result = $task->getResult();
        $outputData[TaskResultInterface::RESULT] = $result;
        return $outputData;
    }
}
