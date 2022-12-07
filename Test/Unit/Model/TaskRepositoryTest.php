<?php

namespace SearchSpring\Feed\Test\Unit\Model;

use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SearchSpring\Feed\Api\Data\TaskInterface;
use SearchSpring\Feed\Api\Data\TaskSearchResultsInterfaceFactory;
use SearchSpring\Feed\Api\Data\TaskSearchResultsInterface;
use SearchSpring\Feed\Model\ResourceModel\Task as TaskResource;
use SearchSpring\Feed\Model\ResourceModel\Task\CollectionFactory;
use SearchSpring\Feed\Model\ResourceModel\Task\Collection;
use SearchSpring\Feed\Model\Task;
use SearchSpring\Feed\Model\TaskFactory;
use SearchSpring\Feed\Model\TaskRepository;

class TaskRepositoryTest extends TestCase
{
    /**
     * @var MockObject|TaskFactory|TaskFactory&MockObject
     */
    private $taskFactoryMock;

    /**
     * @var MockObject|TaskResource|TaskResource&MockObject
     */
    private $taskResourceMock;

    /**
     * @var MockObject|CollectionFactory|CollectionFactory&MockObject
     */
    private $collectionFactoryMock;

    /**
     * @var SearchCriteriaBuilder|SearchCriteriaBuilder&MockObject|MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var MockObject|TaskSearchResultsInterfaceFactory|TaskSearchResultsInterfaceFactory&MockObject
     */
    private $searchResultsFactoryMock;

    /**
     * @var CollectionProcessorInterface&MockObject|MockObject
     */
    private $collectionProcessorMock;

    /**
     * @var JoinProcessorInterface&MockObject|MockObject
     */
    private $joinProcessorMock;

    /**
     * @var TaskRepository
     */
    private $taskRepository;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->taskFactoryMock = $this->createMock(TaskFactory::class);
        $this->taskResourceMock = $this->createMock(TaskResource::class);
        $this->collectionFactoryMock = $this->createMock(CollectionFactory::class);
        $this->searchCriteriaBuilderMock = $this->createMock(SearchCriteriaBuilder::class);
        $this->searchResultsFactoryMock = $this->createMock(TaskSearchResultsInterfaceFactory::class);
        $this->collectionProcessorMock = $this->createMock(CollectionProcessorInterface::class);
        $this->joinProcessorMock = $this->createMock(JoinProcessorInterface::class);
        $this->taskRepository = new TaskRepository(
            $this->taskFactoryMock,
            $this->taskResourceMock,
            $this->collectionFactoryMock,
            $this->searchCriteriaBuilderMock,
            $this->searchResultsFactoryMock,
            $this->collectionProcessorMock,
            $this->joinProcessorMock
        );
    }

    /**
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function testSave()
    {
        $taskMock = $this->getMockBuilder(Task::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->taskResourceMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();

        $this->assertSame($taskMock, $this->taskRepository->save($taskMock));
    }

    /**
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testDeleteById()
    {
        $taskId = 123;

        $taskMock = $this->getMockBuilder(Task::class)
            ->disableOriginalConstructor()
            ->getMock();
        $taskMock->expects($this->any())
            ->method('getEntityId')
            ->willReturn($taskId);
        $this->taskFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($taskMock);
        $this->taskResourceMock->expects($this->once())
            ->method('load')
            ->with($taskMock, $taskId)
            ->willReturnSelf();
        $this->taskResourceMock->expects($this->once())
            ->method('delete')
            ->with($taskMock);

        $this->assertNull($this->taskRepository->deleteById($taskId));
    }

    /**
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGet()
    {
        $taskId = 1;
        $taskMock = $this->getMockBuilder(Task::class)->disableOriginalConstructor()->getMock();
        $this->taskFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($taskMock);
        $this->taskResourceMock->expects($this->once())
            ->method('load')
            ->with($taskMock, $taskId)
            ->willReturnSelf();
        $taskMock->expects($this->once())
            ->method('getEntityId')
            ->willReturn($taskId);

        $this->assertSame($taskMock, $this->taskRepository->get($taskId));
    }

    public function testGetList()
    {
        $collectionSize = 1;
        $collectionItems = [
            $this->getMockBuilder(Task::class)->disableOriginalConstructor()->getMock()
        ];

        $taskCollectionMock = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->collectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($taskCollectionMock);
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $searchResultMock = $this->getMockForAbstractClass(TaskSearchResultsInterface::class);

        $this->collectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($taskCollectionMock);
        $this->joinProcessorMock->expects($this->once())
            ->method('process')
            ->with($taskCollectionMock, TaskInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $taskCollectionMock);
        $this->searchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultMock);
        $searchResultMock->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteriaMock);

        $taskCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn($collectionItems);
        $searchResultMock->expects($this->once())
            ->method('setItems')
            ->with($collectionItems)
            ->willReturnSelf();

        $taskCollectionMock->expects($this->once())
            ->method('getSize')
            ->willReturn($collectionSize);
        $searchResultMock->expects($this->once())
            ->method('setTotalCount')
            ->with($collectionSize);

        $this->assertSame($searchResultMock, $this->taskRepository->getList($searchCriteriaMock));
    }
}
