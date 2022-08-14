<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Task;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use SearchSpring\Feed\Api\Data\TaskInterface;
use SearchSpring\Feed\Exception\CouldNotFetchResultException;

interface ResultFetcherInterface
{
    /**
     * @param TaskInterface $task
     * @return mixed
     * @throws CouldNotFetchResultException
     * @throws LocalizedException
     * @throws Exception
     */
    public function fetch(TaskInterface $task);
}
