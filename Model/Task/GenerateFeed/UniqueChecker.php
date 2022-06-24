<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Task\GenerateFeed;

use Magento\Framework\Api\SearchCriteriaBuilder;
use SearchSpring\Feed\Api\Data\TaskInterface;
use SearchSpring\Feed\Api\MetadataInterface;
use SearchSpring\Feed\Api\TaskRepositoryInterface;
use SearchSpring\Feed\Model\Task\UniqueCheckerInterface;

class UniqueChecker implements UniqueCheckerInterface
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
     * UniqueChecker constructor.
     * @param TaskRepositoryInterface $taskRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        TaskRepositoryInterface $taskRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->taskRepository = $taskRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param array $payload
     * @return bool
     */
    public function check(array $payload): bool
    {
        $tasks = $this->getSuitableTasks();
        if (empty($tasks)) {
            return true;
        }

        $result = true;
        foreach ($tasks as $task) {
            $taskPayload = $task->getPayload();
            if ($this->comparePayloads($taskPayload, $payload)) {
                $result = false;
                break;
            }
        }

        return $result;
    }

    /**
     * @param array $firstPayload
     * @param array $secondPayload
     * @return bool
     */
    private function comparePayloads(array $firstPayload, array $secondPayload) : bool
    {
        $diff = array_diff_key($firstPayload, $secondPayload);
        if (!empty($diff)) {
            return false;
        }

        $diff = array_diff_key($secondPayload, $firstPayload);
        if (!empty($diff)) {
            return false;
        }

        $result = true;
        foreach ($firstPayload as $key => $value) {
            $checkValue = $secondPayload[$key];
            if ($value != $checkValue) {
                $result = false;
                break;
            }
        }

        return $result;
    }

    /**
     * @return TaskInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getSuitableTasks() : array
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(TaskInterface::TYPE, MetadataInterface::FEED_GENERATION_TASK_CODE)
            ->addFilter(
                TaskInterface::STATUS,
                [MetadataInterface::TASK_STATUS_PENDING, MetadataInterface::TASK_STATUS_PROCESSING],
                'in'
            )
            ->create();
        return $this->taskRepository->getList($searchCriteria)->getItems();
    }
}
