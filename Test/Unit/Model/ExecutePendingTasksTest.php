<?php

namespace SearchSpring\Feed\Test\Unit\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Psr\Log\LoggerInterface;
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
            ->willReturn([]);

        $this->assertSame([], $this->executePendingTasks->execute());
    }
}
