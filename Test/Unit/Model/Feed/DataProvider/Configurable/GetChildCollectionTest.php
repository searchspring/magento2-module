<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\DataProvider\Configurable;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Product\Collection;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Product\CollectionFactory
    as ProductCollectionFactory;
use SearchSpring\Feed\Model\Feed\DataProvider\Configurable\GetChildCollection;

class GetChildCollectionTest extends \PHPUnit\Framework\TestCase
{
    private $statusMock;

    private $productCollectionFactoryMock;

    private $getChildCollection;

    public function setUp(): void
    {
        $this->statusMock = $this->createMock(Status::class);
        $this->productCollectionFactoryMock = $this->createMock(ProductCollectionFactory::class);
        $this->getChildCollection = new GetChildCollection(
            $this->productCollectionFactoryMock,
            $this->statusMock
        );
    }

    public function testExecute()
    {
        $productCollectionMock = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->productCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($productCollectionMock);
        $productCollectionMock->expects($this->once())
            ->method('addAttributeToSelect')
            ->withAnyParameters()
            ->willReturnSelf();
        $productCollectionMock->expects($this->once())
            ->method('addAttributeToFilter')
            ->withAnyParameters()
            ->willReturnSelf();
        $productCollectionMock->expects($this->once())
            ->method('addPriceData')
            ->willReturnSelf();

        $this->assertSame($productCollectionMock, $this->getChildCollection->execute([]));
    }
}
