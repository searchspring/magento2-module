<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\Collection;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\CatalogInventory\Model\ResourceModel\Stock\Status;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\Collection\StockModifier;

class StockModifierTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        $this->statusMock = $this->createMock(Status::class);
        $this->stockModifier = new StockModifier($this->statusMock);
    }

    public function testModify()
    {
        $includeOutOfStock = false;
        $feedSpecificationMock = $this->getMockForAbstractClass(FeedSpecificationInterface::class);
        $feedSpecificationMock->expects($this->once())
            ->method('getIncludeOutOfStock')
            ->willReturn($includeOutOfStock);
        $stockFlag = 'has_stock_status_filter';
        $collectionMock = $this->createMock(Collection::class);
        $collectionMock->expects($this->once())
            ->method('hasFlag')
            ->with($stockFlag)
            ->willReturn(false);
        $this->statusMock->expects($this->once())
            ->method('addStockDataToCollection')
            ->with($collectionMock, !$includeOutOfStock)
            ->willReturnSelf();
        $collectionMock->expects($this->once())
            ->method('setFlag')
            ->with($stockFlag, true)
            ->willReturnSelf();

        $this->assertSame($collectionMock, $this->stockModifier->modify($collectionMock, $feedSpecificationMock));
    }
}
