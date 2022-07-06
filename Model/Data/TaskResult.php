<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Data;

use Magento\Framework\Api\AbstractSimpleObject;
use SearchSpring\Feed\Api\Data\TaskInterface;
use SearchSpring\Feed\Api\Data\TaskResultInterface;

class TaskResult extends AbstractSimpleObject implements TaskResultInterface
{

    /**
     * @return TaskInterface|null
     */
    public function getTask(): ?TaskInterface
    {
        return $this->_get(self::TASK);
    }

    /**
     * @param TaskInterface $task
     * @return TaskResultInterface
     */
    public function setTask(TaskInterface $task): TaskResultInterface
    {
        return $this->setData(self::TASK, $task);
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->_get(self::RESULT);
    }

    /**
     * @param $result
     * @return mixed
     */
    public function setResult($result)
    {
        return $this->setData(self::RESULT, $result);
    }
}
