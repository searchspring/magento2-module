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

use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface;
use SearchSpring\Feed\Api\Data\TaskErrorInterface;
use SearchSpring\Feed\Api\Data\TaskErrorInterfaceFactory;
use SearchSpring\Feed\Api\Data\TaskInterface;
use SearchSpring\Feed\Api\MetadataInterface;
use SearchSpring\Feed\Api\TaskRepositoryInterface;
use SearchSpring\Feed\Exception\GenericException;
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

    private $executeTask;

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

    public function testExecuteExceptionCase()
    {
        $type = 'type';
        $time = '10-10-1990 12:40';

        $taskErrorMock = $this->createMock(TaskErrorInterface::class);
        $taskMock = $this->createMock(TaskInterface::class);
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

        $taskMock->expects($this->at(2))
            ->method('setStatus')
            ->with(MetadataInterface::TASK_STATUS_PROCESSING)
            ->willReturnSelf();

        $this->taskRepositoryMock->expects($this->exactly(2))
            ->method('save')
            ->willReturn($taskMock);
        $executorMock->expects($this->once())
            ->method('execute')
            ->with($taskMock)
            ->willThrowException(new \Exception('exception message'));
        $this->taskErrorFactoryMock->expects($this->once())
            ->method('create')
            ->wilLReturn($taskErrorMock);
        $this->loggerMock->expects($this->once())
            ->method('error')
            ->withAnyParameters()
            ->willReturn(0);
        $taskErrorMock->expects($this->once())
            ->method('setMessage')
            ->with('exception message')
            ->willReturnSelf();
        $taskErrorMock->expects($this->once())
            ->method('setCode')
            ->with(GenericException::CODE)
            ->willReturnSelf();
        $taskMock->expects($this->at(3))
            ->method('setStatus')
            ->with(MetadataInterface::TASK_STATUS_ERROR)
            ->willReturnSelf();
        $taskMock->expects($this->once())
            ->method('setError')
            ->with($taskErrorMock)
            ->willReturnSelf();
        $taskMock->expects($this->once())
            ->method('setEndedAt')
            ->with($time)
            ->willReturnSelf();

        $this->assertSame(null, $this->executeTask->execute($taskMock));
    }
}
