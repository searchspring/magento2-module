<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Api\Data;

interface TaskResultInterface
{
    const TASK = 'task';
    const RESULT = 'result';
    /**
     * @return \SearchSpring\Feed\Api\Data\TaskInterface|null
     */
    public function getTask() : ?TaskInterface;

    /**
     * @param \SearchSpring\Feed\Api\Data\TaskInterface $task
     * @return TaskResultInterface
     */
    public function setTask(TaskInterface $task) : self;

    /**
     * @return mixed
     */
    public function getResult();

    /**
     * @param $result
     * @return mixed
     */
    public function setResult($result);
}
