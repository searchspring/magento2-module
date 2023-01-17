<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\Collection;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\CatalogRule\Model\ResourceModel\Product\CollectionProcessor;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\Collection\PricesModifier;

class PricesModifierTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        $this->collectionProcessorMock = $this->createMock(CollectionProcessor::class);
        $this->pricesModifier = new PricesModifier(
            $this->collectionProcessorMock
        );
    }

    public function testModify()
    {
        $feedSpecificationMock = $this->getMockForAbstractClass(FeedSpecificationInterface::class);
        $feedSpecificationMock->expects($this->once())
            ->method('getIgnoreFields')
            ->willReturn([]);
        $collectionMock = $this->createMock(Collection::class);
        $collectionMock->expects($this->once())
            ->method('addPriceData')
            ->willReturnSelf();
        $this->collectionProcessorMock->expects($this->once())
            ->method('addPriceData')
            ->with($collectionMock)
            ->willReturnSelf();

        $this->assertSame($collectionMock, $this->pricesModifier->modify($collectionMock, $feedSpecificationMock));
    }
}
