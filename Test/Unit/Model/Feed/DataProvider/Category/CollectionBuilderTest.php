<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\DataProvider\Category;

use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use SearchSpring\Feed\Model\Feed\DataProvider\Category\CollectionBuilder;
use SearchSpring\Feed\Model\Feed\Specification\Feed;

class CollectionBuilderTest extends \PHPUnit\Framework\TestCase
{
    private $collectionFactoryMock;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->collectionFactoryMock = $this->createMock(CollectionFactory::class);
        $this->collectionBuilder = new CollectionBuilder($this->collectionFactoryMock);
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function testBuildCollection()
    {
        $storeCode = 'default';

        $feedSpecificationMock = $this->getMockBuilder(Feed::class)
            ->disableOriginalConstructor()
            ->getMock();
        $collectionMock = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->collectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($collectionMock);
        $feedSpecificationMock->expects($this->once())
            ->method('getStoreCode')
            ->willReturn($storeCode);
        $collectionMock->expects($this->once())
            ->method('setStore')
            ->with($storeCode);
        $feedSpecificationMock->expects($this->once())
            ->method('getIncludeMenuCategories')
            ->willReturn(false);
        $feedSpecificationMock->expects($this->once())
            ->method('getIncludeUrlHierarchy')
            ->willReturn(false);
        $collectionMock->expects($this->once())
            ->method('addAttributeToSelect')
            ->withAnyParameters();
        $collectionMock->expects($this->any())
            ->method('addAttributeToFilter')
            ->withAnyParameters()
            ->willReturnSelf();

        $this->assertSame(
            $collectionMock,
            $this->collectionBuilder->buildCollection([], $feedSpecificationMock)
        );
    }
}
