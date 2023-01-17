<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\DataProvider\Configurable;

use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Attribute\Collection;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Attribute\CollectionFactory
    as AttributeCollectionFactory;
use SearchSpring\Feed\Model\Feed\DataProvider\Configurable\GetAttributesCollection;

class GetAttributesCollectionTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        $this->joinProcessorMock = $this->createMock(JoinProcessorInterface::class);
        $this->attributeCollectionFactoryMock = $this->createMock(AttributeCollectionFactory::class);
        $this->getAttributesCollection = new GetAttributesCollection(
            $this->joinProcessorMock,
            $this->attributeCollectionFactoryMock
        );
    }

    public function testExecute()
    {
        $attributesCollectionMock = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->attributeCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($attributesCollectionMock);
        $this->joinProcessorMock->expects($this->once())
            ->method('process')
            ->with($attributesCollectionMock);
        $attributesCollectionMock->expects($this->once())
            ->method('orderByPosition')
            ->willReturnSelf();

        $this->assertSame($attributesCollectionMock, $this->getAttributesCollection->execute([]));
    }
}
