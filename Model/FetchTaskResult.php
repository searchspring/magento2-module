<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model;

use SearchSpring\Feed\Api\Data\TaskResultInterface;
use SearchSpring\Feed\Api\Data\TaskResultInterfaceFactory;
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
     * @var TaskResultInterfaceFactory
     */
    private $taskResultFactory;

    /**
     * FetchTaskResult constructor.
     * @param TaskRepositoryInterface $taskRepository
     * @param ResultFetcherPool $resultFetcherPool
     * @param TaskResultInterfaceFactory $taskResultFactory
     */
    public function __construct(
        TaskRepositoryInterface $taskRepository,
        ResultFetcherPool $resultFetcherPool,
        TaskResultInterfaceFactory $taskResultFactory
    ) {
        $this->taskRepository = $taskRepository;
        $this->resultFetcherPool = $resultFetcherPool;
        $this->taskResultFactory = $taskResultFactory;
    }

    /**
     * @param int $id
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(int $id) : TaskResultInterface
    {
        $task = $this->taskRepository->get($id);
        if (!$task->getStatus() === MetadataInterface::TASK_STATUS_SUCCESS) {
            throw new \Exception();
        }

        $fetcher = $this->resultFetcherPool->get($task->getType());
        $result = $fetcher->fetch($task);
        /** @var TaskResultInterface $taskResult */
        $taskResult = $this->taskResultFactory->create();
        $taskResult->setTask($task)
            ->setResult($result);

        return $taskResult;
    }
}
