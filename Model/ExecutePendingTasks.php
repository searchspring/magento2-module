<?php
/**
 * Copyright (C) 2023 Searchspring <https://searchspring.com>
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

namespace SearchSpring\Feed\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ExecutePendingTasks constructor.
     * @param TaskRepositoryInterface $taskRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ExecuteTaskInterface $executeTask
     * @param LoggerInterface $logger
     */
    public function __construct(
        TaskRepositoryInterface $taskRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ExecuteTaskInterface $executeTask,
        LoggerInterface $logger
    ) {
        $this->taskRepository = $taskRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->executeTask = $executeTask;
        $this->logger = $logger;
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
            try {
                $result[$task->getEntityId()] = $this->executeTask->execute($task);
            } catch (\Throwable $exception) {
                $this->logger->error($exception->getMessage(), ['trace' => $exception->getTraceAsString()]);
            }
        }

        return $result;
    }
}
