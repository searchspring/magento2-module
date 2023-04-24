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

namespace SearchSpring\Feed\Test\Unit\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Psr\Log\LoggerInterface;
use SearchSpring\Feed\Api\Data\TaskInterface;
use SearchSpring\Feed\Api\Data\TaskSearchResultsInterface;
use SearchSpring\Feed\Api\ExecuteTaskInterface;
use SearchSpring\Feed\Api\TaskRepositoryInterface;
use SearchSpring\Feed\Model\ExecutePendingTasks;

class ExecutePendingTasksTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var TaskRepositoryInterface
     */
    private $taskRepositoryMock;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var ExecuteTaskInterface
     */
    private $executeTaskMock;

    /**
     * @var LoggerInterface
     */
    private $loggerMock;

    private $executePendingTasks;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->taskRepositoryMock = $this->createMock(TaskRepositoryInterface::class);
        $this->searchCriteriaBuilderMock = $this->createMock(SearchCriteriaBuilder::class);
        $this->executeTaskMock = $this->createMock(ExecuteTaskInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->executePendingTasks = new ExecutePendingTasks(
            $this->taskRepositoryMock,
            $this->searchCriteriaBuilderMock,
            $this->executeTaskMock,
            $this->loggerMock
        );
    }

    public function testExecute()
    {
        $entityId = 1;
        $executeResult = ['payload' => 'test'];
        $taskInterfaceMock = $this->createMock(TaskInterface::class);
        $searchCriteriaMock = $this->getMockBuilder(SearchCriteriaInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $searchResultsMock = $this->getMockBuilder(TaskSearchResultsInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with('status', 'pending')
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);
        $this->taskRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($searchResultsMock);
        $searchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$taskInterfaceMock]);
        $taskInterfaceMock->expects($this->once())
            ->method('getEntityId')
            ->willReturn($entityId);
        $this->executeTaskMock->expects($this->once())
            ->method('execute')
            ->with($taskInterfaceMock)
            ->willReturn($executeResult);

        $this->assertSame([$entityId => $executeResult], $this->executePendingTasks->execute());
    }

    public function testExecuteExceptionCase()
    {
        $taskInterfaceMock = $this->createMock(TaskInterface::class);
        $searchCriteriaMock = $this->getMockBuilder(SearchCriteriaInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $searchResultsMock = $this->getMockBuilder(TaskSearchResultsInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with('status', 'pending')
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);
        $this->taskRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($searchResultsMock);
        $searchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$taskInterfaceMock]);
        $taskInterfaceMock->expects($this->once())
            ->method('getEntityId')
            ->willReturn(1);
        $this->executeTaskMock->expects($this->once())
            ->method('execute')
            ->with($taskInterfaceMock)
            ->willThrowException(new \Exception('message'));
        $this->loggerMock->expects($this->once())
            ->method('error')
            ->willReturn(true);

        $this->executePendingTasks->execute();
    }
}
