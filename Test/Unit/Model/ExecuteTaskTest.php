<?php

namespace SearchSpring\Feed\Test\Unit\Model;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface;
use SearchSpring\Feed\Api\Data\TaskErrorInterfaceFactory;
use SearchSpring\Feed\Api\TaskRepositoryInterface;
use SearchSpring\Feed\Model\ExecuteTask;
use SearchSpring\Feed\Model\Task;
use SearchSpring\Feed\Model\Task\ExecutorPool;

class ExecuteTaskTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ExecutorPool
     */
    private $executorPoolMock;

    /**
     * @var TaskRepositoryInterface
     */
    private $taskRepositoryMock;

    /**
     * @var DateTime
     */
    private $dateTimeMock;

    /**
     * @var TaskErrorInterfaceFactory
     */
    private $taskErrorFactoryMock;

    /**
     * @var LoggerInterface
     */
    private $loggerMock;

    public function setUp(): void
    {
        $this->executorPoolMock = $this->createMock(ExecutorPool::class);
        $this->taskRepositoryMock = $this->createMock(TaskRepositoryInterface::class);
        $this->dateTimeMock = $this->createMock(DateTime::class);
        $this->taskErrorFactoryMock = $this->createMock(TaskErrorInterfaceFactory::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->executeTask = new ExecuteTask(
            $this->executorPoolMock,
            $this->taskRepositoryMock,
            $this->dateTimeMock,
            $this->taskErrorFactoryMock,
            $this->loggerMock
        );
    }

    public function testExecute()
    {
        $type = 'type';
        $time = '10-10-1990 12:40';

        $taskMock = $this->getMockBuilder(Task::class)
            ->disableOriginalConstructor()
            ->getMock();
        $executorMock = $this->getMockBuilder(Task\GenerateFeed\Executor::class)
            ->disableOriginalConstructor()
            ->getMock();
        $taskMock->expects($this->once())
            ->method('getType')
            ->willReturn($type);
        $this->executorPoolMock->expects($this->once())
            ->method('get')
            ->with($type)
            ->willReturn($executorMock);
        $this->dateTimeMock->expects($this->exactly(2))
            ->method('gmtDate')
            ->willReturn($time);

        $taskMock->expects($this->once())
            ->method('setStartedAt')
            ->with($time)
            ->willReturnSelf();

        $taskMock->expects($this->any())
            ->method('setStatus')
            ->withAnyParameters()
            ->willReturnSelf();

        $this->taskRepositoryMock->expects($this->exactly(2))
            ->method('save')
            ->willReturn($taskMock);
        $executorMock->expects($this->once())
            ->method('execute')
            ->with($taskMock)
            ->willReturn(true);
        $taskMock->expects($this->once())
            ->method('setEndedAt')
            ->with($time)
            ->willReturnSelf();

        $this->assertTrue($this->executeTask->execute($taskMock));
    }
}
