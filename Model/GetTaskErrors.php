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

namespace SearchSpring\Feed\Model;

use Magento\Framework\App\ResourceConnection;
use SearchSpring\Feed\Api\Data\TaskErrorItemInterface;
use SearchSpring\Feed\Api\Data\TaskErrorItemInterfaceFactory;
use SearchSpring\Feed\Api\Data\TaskErrorListResponseInterface;
use SearchSpring\Feed\Api\Data\TaskErrorListResponseInterfaceFactory;
use SearchSpring\Feed\Api\GetTaskErrorsInterface;
use SearchSpring\Feed\Model\ResourceModel\Task as TaskResource;

class GetTaskErrors implements GetTaskErrorsInterface
{
    private const MAX_PAGE_SIZE = 200;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var TaskErrorItemInterfaceFactory
     */
    private $itemFactory;
    /**
     * @var TaskErrorListResponseInterfaceFactory
     */
    private $responseFactory;

    /**
     * @param ResourceConnection $resourceConnection
     * @param TaskErrorItemInterfaceFactory $itemFactory
     * @param TaskErrorListResponseInterfaceFactory $responseFactory
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        TaskErrorItemInterfaceFactory $itemFactory,
        TaskErrorListResponseInterfaceFactory $responseFactory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->itemFactory = $itemFactory;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        int $currentPage = 1,
        int $pageSize = 20,
        ?int $taskId = null
    ): TaskErrorListResponseInterface {
        $currentPage = max(1, $currentPage);
        $pageSize = min(max(1, $pageSize), self::MAX_PAGE_SIZE);

        $connection = $this->resourceConnection->getConnection();
        $errorTable = $this->resourceConnection->getTableName(TaskResource::ERROR_TABLE);
        $taskTable = $this->resourceConnection->getTableName(TaskResource::TABLE);

        $countSelect = $connection->select()->from($errorTable, ['COUNT(*)']);
        $dataSelect = $connection->select()
            ->from(['error' => $errorTable], ['task_id', 'code', 'message'])
            ->joinLeft(
                ['task' => $taskTable],
                'task.entity_id = error.task_id',
                [
                    'task_type' => 'type',
                    'task_status' => 'status',
                    'task_created_at' => 'created_at',
                    'task_started_at' => 'started_at',
                    'task_ended_at' => 'ended_at',
                ]
            )
            ->order('error.task_id DESC')
            ->limitPage($currentPage, $pageSize);

        if ($taskId !== null) {
            $countSelect->where('task_id = ?', $taskId);
            $dataSelect->where('error.task_id = ?', $taskId);
        }

        $items = [];
        foreach ($connection->fetchAll($dataSelect) as $row) {
            /** @var TaskErrorItemInterface $item */
            $item = $this->itemFactory->create();
            $item->setTaskId((int) $row['task_id'])
                ->setCode((int) $row['code'])
                ->setMessage((string) $row['message'])
                ->setTaskType($row['task_type'])
                ->setTaskStatus($row['task_status'])
                ->setTaskCreatedAt($row['task_created_at'])
                ->setTaskStartedAt($row['task_started_at'])
                ->setTaskEndedAt($row['task_ended_at']);
            $items[] = $item;
        }

        /** @var TaskErrorListResponseInterface $response */
        $response = $this->responseFactory->create();
        return $response
            ->setItems($items)
            ->setTotal((int) $connection->fetchOne($countSelect))
            ->setCurrentPage($currentPage)
            ->setPageSize($pageSize);
    }
}
