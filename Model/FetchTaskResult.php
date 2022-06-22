<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model;

use SearchSpring\Feed\Api\FetchTaskResultInterface;
use SearchSpring\Feed\Api\MetadataInterface;
use SearchSpring\Feed\Api\TaskRepositoryInterface;
use SearchSpring\Feed\Model\Task\ResultFetcherPool;

class FetchTaskResult implements FetchTaskResultInterface
{
    /**
     * @var TaskRepositoryInterface
     */
    private $taskRepository;
    /**
     * @var ResultFetcherPool
     */
    private $resultFetcherPool;

    /**
     * FetchTaskResult constructor.
     * @param TaskRepositoryInterface $taskRepository
     * @param ResultFetcherPool $resultFetcherPool
     */
    public function __construct(
        TaskRepositoryInterface $taskRepository,
        ResultFetcherPool $resultFetcherPool
    ) {
        $this->taskRepository = $taskRepository;
        $this->resultFetcherPool = $resultFetcherPool;
    }

    /**
     * @param int $id
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(int $id)
    {
        $task = $this->taskRepository->get($id);
        if (!$task->getStatus() === MetadataInterface::TASK_STATUS_SUCCESS) {
            throw new \Exception();
        }

        $fetcher = $this->resultFetcherPool->get($task->getType());
        return $fetcher->fetch($task);
    }
}
