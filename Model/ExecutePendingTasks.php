<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use SearchSpring\Feed\Api\Data\TaskInterface;
use SearchSpring\Feed\Api\ExecutePendingTasksInterface;
use SearchSpring\Feed\Api\ExecuteTaskInterface;
use SearchSpring\Feed\Api\MetadataInterface;
use SearchSpring\Feed\Api\TaskRepositoryInterface;

class ExecutePendingTasks implements ExecutePendingTasksInterface
{
    /**
     * @var TaskRepositoryInterface
     */
    private $taskRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var ExecuteTaskInterface
     */
    private $executeTask;

    /**
     * ExecutePendingTasks constructor.
     * @param TaskRepositoryInterface $taskRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ExecuteTaskInterface $executeTask
     */
    public function __construct(
        TaskRepositoryInterface $taskRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ExecuteTaskInterface $executeTask
    ) {
        $this->taskRepository = $taskRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->executeTask = $executeTask;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function execute(): array
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(TaskInterface::STATUS, MetadataInterface::TASK_STATUS_PENDING)
            ->create();
        $taskList = $this->taskRepository->getList($searchCriteria);
        $result = [];
        foreach ($taskList->getItems() as $task) {
            $result[] = $this->executeTask->execute($task);
        }

        return $result;
    }
}
