<?php

namespace SearchSpring\Feed\Test\Unit\Cron;

use SearchSpring\Feed\Api\ExecutePendingTasksInterface;
use SearchSpring\Feed\Cron\ExecuteTasks;

class ExecuteTasksTest extends \PHPUnit\Framework\TestCase
{
    private $executePendingTaskInterfaceMock;

    public function setUp(): void
    {
        $this->executePendingTaskInterfaceMock = $this->createMock(ExecutePendingTasksInterface::class);
        $this->executeTasks = new ExecuteTasks($this->executePendingTaskInterfaceMock);
    }

    public function testExecute()
    {
        $this->executePendingTaskInterfaceMock->expects($this->once())
            ->method('execute')
            ->willReturn([]);

        $this->assertSame(null, $this->executeTasks->execute());
    }
}
