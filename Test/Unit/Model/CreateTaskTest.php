<?php

namespace SearchSpring\Feed\Test\Unit\Model;

use Magento\Framework\Validation\ValidationResult;
use SearchSpring\Feed\Api\Data\TaskInterfaceFactory;
use SearchSpring\Feed\Api\TaskRepositoryInterface;
use SearchSpring\Feed\Exception\UniqueTaskException;
use SearchSpring\Feed\Exception\ValidationException;
use SearchSpring\Feed\Model\CreateTask;
use SearchSpring\Feed\Model\Task;
use SearchSpring\Feed\Model\Task\TypeList;
use SearchSpring\Feed\Model\Task\UniqueCheckerInterface;
use SearchSpring\Feed\Model\Task\UniqueCheckerPool;
use SearchSpring\Feed\Model\Task\ValidatorInterface;
use SearchSpring\Feed\Model\Task\ValidatorPool;

class CreateTaskTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var TaskRepositoryInterface
     */
    private $taskRepositoryMock;

    /**
     * @var TaskInterfaceFactory
     */
    private $taskFactoryMock;

    /**
     * @var ValidatorPool
     */
    private $validatorPoolMock;

    /**
     * @var TypeList
     */
    private $typeListMock;

    /**
     * @var UniqueCheckerPool
     */
    private $uniqueCheckerPoolMock;

    private $createTask;

    public function setUp(): void
    {
        $this->taskRepositoryMock = $this->createMock(TaskRepositoryInterface::class);
        $this->taskFactoryMock = $this->createMock(TaskInterfaceFactory::class);
        $this->validatorPoolMock = $this->createMock(ValidatorPool::class);
        $this->typeListMock = $this->createMock(TypeList::class);
        $this->uniqueCheckerPoolMock = $this->createMock(UniqueCheckerPool::class);
        $this->createTask = new CreateTask(
            $this->taskRepositoryMock,
            $this->taskFactoryMock,
            $this->validatorPoolMock,
            $this->typeListMock,
            $this->uniqueCheckerPoolMock
        );
    }

    public function testExecute()
    {
        $type = 'type';
        $payload = [];
        
        $this->typeListMock->expects($this->once())
            ->method('exist')
            ->willReturn(true);

        $validationResultMock = $this->getMockBuilder(ValidationResult::class)
            ->disableOriginalConstructor()
            ->getMock();
        $validatorMock = $this->getMockBuilder(ValidatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->validatorPoolMock->expects($this->once())
            ->method('get')
            ->with($type)
            ->willReturn($validatorMock);
        $validatorMock->expects($this->once())
            ->method('validate')
            ->with($payload)
            ->willReturn($validationResultMock);
        $validationResultMock->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $uniqueCheckerMock = $this->getMockBuilder(UniqueCheckerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->uniqueCheckerPoolMock->expects($this->once())
            ->method('get')
            ->willReturn($uniqueCheckerMock);
        $uniqueCheckerMock->expects($this->once())
            ->method('check')
            ->with($payload)
            ->willReturn(true);

        $taskMock = $this->getMockBuilder(Task::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->taskFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($taskMock);
        $taskMock->expects($this->once())
            ->method('setType')
            ->with($type)
            ->willReturnSelf();
        $taskMock->expects($this->once())
            ->method('setPayload')
            ->with($payload)
            ->willReturnSelf();
        $taskMock->expects($this->once())
            ->method('setStatus')
            ->with('pending')
            ->willReturnSelf();
        $this->taskRepositoryMock->expects($this->once())
            ->method('save')
            ->willReturn($taskMock);

        $this->assertSame($taskMock, $this->createTask->execute($type, $payload));
    }

    public function testExecuteExceptionCase()
    {
        $type = 'testType';
        $this->expectException(\Exception::class);
        $this->createTask->execute($type, '');
    }

    public function testExecuteValidationExceptionCase()
    {
        $type = 'testType';
        $this->typeListMock->expects($this->once())
            ->method('exist')
            ->with($type)
            ->willReturn(false);
        $this->expectException(ValidationException::class);
        $this->createTask->execute($type, []);
    }

    public function testExecuteValidationExceptionOnValidationCase()
    {
        $type = 'testType';
        $validationResultMock = $this->createMock(ValidationResult::class);
        $validatorMock = $this->createMock(ValidatorInterface::class);
        $this->typeListMock->expects($this->once())
            ->method('exist')
            ->with($type)
            ->willReturn(true);
        $this->validatorPoolMock->expects($this->once())
            ->method('get')
            ->with($type)
            ->willReturn($validatorMock);
        $validatorMock->expects($this->once())
            ->method('validate')
            ->with([])
            ->willReturn($validationResultMock);
        $validationResultMock->expects($this->once())
            ->method('isValid')
            ->willReturn(false);
        $validationResultMock->expects($this->once())
            ->method('getErrors')
            ->willReturn(['error']);
        $this->expectException(ValidationException::class);
        $this->createTask->execute($type, []);
    }


    public function testExecuteValidationExceptionOnUniqueTaskCase()
    {
        $type = 'testType';
        $uniqueCheckerInterfaceMock = $this->createMock(UniqueCheckerInterface::class);
        $validationResultMock = $this->createMock(ValidationResult::class);
        $validatorMock = $this->createMock(ValidatorInterface::class);
        $this->typeListMock->expects($this->once())
            ->method('exist')
            ->with($type)
            ->willReturn(true);
        $this->validatorPoolMock->expects($this->once())
            ->method('get')
            ->with($type)
            ->willReturn($validatorMock);
        $validatorMock->expects($this->once())
            ->method('validate')
            ->with([])
            ->willReturn($validationResultMock);
        $validationResultMock->expects($this->once())
            ->method('isValid')
            ->willReturn(true);
        $this->uniqueCheckerPoolMock->expects($this->once())
            ->method('get')
            ->with($type)
            ->willReturn($uniqueCheckerInterfaceMock);
        $uniqueCheckerInterfaceMock->expects($this->once())
            ->method('check')
            ->with([])
            ->willReturn(false);
        $this->expectException(UniqueTaskException::class);
        $this->createTask->execute($type, []);
    }
}
