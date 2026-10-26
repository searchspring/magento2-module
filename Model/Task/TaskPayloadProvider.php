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
use Magento\Store\Model\StoreManagerInterface;
use SearchSpring\Feed\Api\Data\TaskInterface;
use SearchSpring\Feed\Api\MetadataInterface;
use SearchSpring\Feed\Api\TaskRepositoryInterface;
use SearchSpring\Feed\Model\Feed\SpecificationBuilderInterface;

/**
 * Finds the most relevant previous task to reuse its payload, e.g. to rebuild the feed specification.
 */
class TaskPayloadProvider
{
    /**
     * Number of tasks loaded per page while searching for a store specific task.
     */
    private const PAGE_SIZE = 100;

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
     * @var SpecificationBuilderInterface
     */
    private $specificationBuilder;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param TaskRepositoryInterface $taskRepository
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param SortOrderBuilderFactory $sortOrderBuilderFactory
     * @param SpecificationBuilderInterface $specificationBuilder
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        TaskRepositoryInterface $taskRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        SortOrderBuilderFactory $sortOrderBuilderFactory,
        SpecificationBuilderInterface $specificationBuilder,
        StoreManagerInterface $storeManager
    ) {
        $this->taskRepository = $taskRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->sortOrderBuilderFactory = $sortOrderBuilderFactory;
        $this->specificationBuilder = $specificationBuilder;
        $this->storeManager = $storeManager;
    }

    /**
     * Latest successful task of the store, otherwise the latest task of the store.
     * Only when the store has no task at all, the latest task of any store is returned.
     *
     * @param string $storeCode
     * @param string $taskType
     * @return TaskInterface|null
     */
    public function getLatestTaskForStore(
        string $storeCode,
        string $taskType = MetadataInterface::FEED_GENERATION_TASK_CODE
    ): ?TaskInterface {
        $latestTask = null;
        $latestStoreTask = null;
        $page = 1;
        // the total count bounds the loop, a collection returns its last page again for a page past the end
        do {
            $searchResults = $this->taskRepository->getList($this->createSearchCriteria($taskType, $page));
            foreach ($searchResults->getItems() as $task) {
                $latestTask = $latestTask ?? $task;
                if ($this->getStoreCode($task->getPayload()) !== $storeCode) {
                    continue;
                }

                if ($task->getStatus() === MetadataInterface::TASK_STATUS_SUCCESS) {
                    return $task;
                }
                $latestStoreTask = $latestStoreTask ?? $task;
            }
        } while ($page++ * self::PAGE_SIZE < $searchResults->getTotalCount());

        return $latestStoreTask ?? $latestTask;
    }

    /**
     * Store code the feed is generated for, with the same defaults as the feed generation:
     * SpecificationBuilder defaults a missing store to "default", an empty store means the current store.
     *
     * @param array $payload
     * @return string
     */
    public function getStoreCode(array $payload): string
    {
        $storeCode = $this->specificationBuilder->build($payload)->getStoreCode();
        if ($storeCode === null || $storeCode === '') {
            $storeCode = (string) $this->storeManager->getDefaultStoreView()->getCode();
        }

        return $storeCode;
    }

    /**
     * @param string $taskType
     * @param int $page
     * @return \Magento\Framework\Api\SearchCriteriaInterface
     */
    private function createSearchCriteria(string $taskType, int $page)
    {
        $sortOrder = $this->sortOrderBuilderFactory->create()
            ->setField(TaskInterface::ENTITY_ID)
            ->setDescendingDirection()
            ->create();

        return $this->searchCriteriaBuilderFactory->create()
            ->addFilter(TaskInterface::TYPE, $taskType)
            ->addSortOrder($sortOrder)
            ->setPageSize(self::PAGE_SIZE)
            ->setCurrentPage($page)
            ->create();
    }
}
