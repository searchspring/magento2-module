<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\Collection;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\Collection\AttributesModifier;
use SearchSpring\Feed\Model\Feed\DataProvider\Attribute\AttributesProviderInterface;

class AttributesModifierTest extends \PHPUnit\Framework\TestCase
{
    private $attributesProviderMock;

    private $attributesModifier;

    public function setUp(): void
    {
        $this->attributesProviderMock = $this->createMock(AttributesProviderInterface::class);
        $this->attributesModifier = new AttributesModifier(
            $this->attributesProviderMock,
            []
        );
    }

    public function testModify()
    {
        $productAttributesMock = $this->createMock(ProductAttributeInterface::class);
        $attributes = [
            $productAttributesMock
        ];
        $feedSpecificationMock = $this->getMockForAbstractClass(FeedSpecificationInterface::class);
        $collectionMock = $this->createMock(Collection::class);
        $this->attributesProviderMock->expects($this->once())
            ->method('getAttributes')
            ->with($feedSpecificationMock)
            ->willReturn($attributes);
        $productAttributesMock->expects($this->once())
            ->method('getAttributeCode')
            ->willReturn('code');

        $collectionMock->expects($this->once())
            ->method('addAttributeToSelect')
            ->with(['code'])
            ->willReturnSelf();

        $this->assertSame(
            $collectionMock,
            $this->attributesModifier->modify($collectionMock, $feedSpecificationMock)
        );
    }
}
