<?php

namespace SearchSpring\Feed\Test\Unit\Model;

use SearchSpring\Feed\Api\AppConfigInterface;
use SearchSpring\Feed\Model\Feed\Collection\ProcessorPool;
use SearchSpring\Feed\Model\Feed\CollectionConfigInterface;
use SearchSpring\Feed\Model\Feed\CollectionProviderInterface;
use SearchSpring\Feed\Model\Feed\ContextManagerInterface;
use SearchSpring\Feed\Model\Feed\DataProviderPool;
use SearchSpring\Feed\Model\Feed\Specification\Feed;
use SearchSpring\Feed\Model\Feed\StorageInterface;
use SearchSpring\Feed\Model\Feed\SystemFieldsList;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use SearchSpring\Feed\Model\Metric\CollectorInterface;

class GenerateFeedTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CollectionProviderInterface
     */
    private $collectionProviderMock;

    /**
     * @var DataProviderPool
     */
    private $dataProviderPoolMock;

    /**
     * @var CollectionConfigInterface
     */
    private $collectionConfigMock;

    /**
     * @var StorageInterface
     */
    private $storageMock;

    /**
     * @var SystemFieldsList
     */
    private $systemFieldsListMock;

    /**
     *
     * @var ContextManagerInterface
     */
    private $contextManagerMock;

    /**
     * @var ProcessorPool
     */
    private $afterLoadProcessorPoolMock;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->collectionProviderMock = $this->createMock(CollectionProviderInterface::class);
        $this->dataProviderPoolMock = $this->createMock(DataProviderPool::class);
        $this->collectionConfigMock = $this->createMock(CollectionConfigInterface::class);
        $this->storageMock = $this->createMock(StorageInterface::class);
        $this->systemFieldsListMock = $this->createMock(SystemFieldsList::class);
        $this->contextManagerMock = $this->createMock(ContextManagerInterface::class);
        $this->afterLoadProcessorPoolMock = $this->createMock(ProcessorPool::class);
        $this->metricCollectorMock = $this->createMock(CollectorInterface::class);
        $this->appConfigMock = $this->createMock(AppConfigInterface::class);
        $this->generateFeed = new \SearchSpring\Feed\Model\GenerateFeed(
            $this->collectionProviderMock,
            $this->dataProviderPoolMock,
            $this->collectionConfigMock,
            $this->storageMock,
            $this->systemFieldsListMock,
            $this->contextManagerMock,
            $this->afterLoadProcessorPoolMock,
            $this->metricCollectorMock,
            $this->appConfigMock
        );
    }

    public function testExecute()
    {
        $pageSize = 10;
        $format = 'format';

        $feedSpecificationMock = $this->getMockBuilder(Feed::class)->disableOriginalConstructor()->getMock();
        $feedSpecificationMock->expects($this->once())
            ->method('getFormat')
            ->willReturn($format);
        $this->storageMock->expects($this->once())
            ->method('isSupportedFormat')
            ->with($format)
            ->willReturn(true);

        $this->contextManagerMock->expects($this->once())
            ->method('setContextFromSpecification')
            ->with($feedSpecificationMock);

        $collectionMock = $this->getMockBuilder(Collection::class)->disableOriginalConstructor()->getMock();
        $this->collectionProviderMock->expects($this->once())
            ->method('getCollection')
            ->willReturn($collectionMock);
        $this->collectionConfigMock->expects($this->once())
            ->method('getPageSize')
            ->willReturn($pageSize);
        $collectionMock->expects($this->once())
            ->method('setPageSize')
            ->with($pageSize);
        $collectionMock->expects($this->once())
            ->method('getLastPageNumber')
            ->willReturn(0);

        $this->contextManagerMock->expects($this->once())
            ->method('resetContext');

        $this->generateFeed->execute($feedSpecificationMock);
    }
}
