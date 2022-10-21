<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Api;

use Magento\Framework\Exception\CouldNotSaveException;
use SearchSpring\Feed\Api\Data\TaskInterface;

interface ExecuteTaskInterface
{
    /**
     * @param TaskInterface $task
     * @return mixed
     * @throws CouldNotSaveException
     */
    public function execute(TaskInterface $task);
}
