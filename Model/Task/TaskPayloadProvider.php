<?php
/**
 * Copyright (C) 2026 Searchspring <https://searchspring.com>
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Task;

use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Api\SortOrderBuilderFactory;
use SearchSpring\Feed\Api\Data\TaskInterface;
use SearchSpring\Feed\Api\MetadataInterface;
use SearchSpring\Feed\Api\TaskRepositoryInterface;

/**
 * Finds the most relevant previous task to reuse its payload, e.g. to rebuild the feed specification.
 */
class TaskPayloadProvider
{
    /**
     * Number of latest tasks scanned while searching for a store specific payload.
     */
    private const SCAN_LIMIT = 50;

    /**
     * @var TaskRepositoryInterface
     */
    private $taskRepository;
    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;
    /**
     * @var SortOrderBuilderFactory
     */
    private $sortOrderBuilderFactory;

    /**
     * @param TaskRepositoryInterface $taskRepository
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param SortOrderBuilderFactory $sortOrderBuilderFactory
     */
    public function __construct(
        TaskRepositoryInterface $taskRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        SortOrderBuilderFactory $sortOrderBuilderFactory
    ) {
        $this->taskRepository = $taskRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->sortOrderBuilderFactory = $sortOrderBuilderFactory;
    }

    /**
     * Latest task for the store, successful tasks preferred.
     * Falls back to the latest task of any store when the store has no task.
     *
     * @param string $storeCode
     * @param string $taskType
     * @return TaskInterface|null
     */
    public function getLatestTaskForStore(
        string $storeCode,
        string $taskType = MetadataInterface::FEED_GENERATION_TASK_CODE
    ): ?TaskInterface {
        $tasks = $this->getLatestTasks($taskType);
        $storeTasks = array_filter($tasks, static function (TaskInterface $task) use ($storeCode) {
            return ($task->getPayload()['store'] ?? null) === $storeCode;
        });

        foreach ($storeTasks as $task) {
            if ($task->getStatus() === MetadataInterface::TASK_STATUS_SUCCESS) {
                return $task;
            }
        }

        $task = reset($storeTasks) ?: reset($tasks);
        return $task instanceof TaskInterface ? $task : null;
    }

    /**
     * @param string $taskType
     * @return TaskInterface[]
     */
    private function getLatestTasks(string $taskType): array
    {
        $sortOrder = $this->sortOrderBuilderFactory->create()
            ->setField(TaskInterface::ENTITY_ID)
            ->setDescendingDirection()
            ->create();

        $searchCriteria = $this->searchCriteriaBuilderFactory->create()
            ->addFilter(TaskInterface::TYPE, $taskType)
            ->addSortOrder($sortOrder)
            ->setPageSize(self::SCAN_LIMIT)
            ->setCurrentPage(1)
            ->create();

        return array_values($this->taskRepository->getList($searchCriteria)->getItems());
    }
}
