<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\Collection;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\Collection\StoreModifier;

class StoreModifierTest extends \PHPUnit\Framework\TestCase
{
    private $storeModifier;

    public function setUp(): void
    {
        $this->storeModifier = new StoreModifier();
    }

    public function testModify()
    {
        $storeCode = 'default';
        $feedSpecificationMock = $this->getMockForAbstractClass(FeedSpecificationInterface::class);
        $feedSpecificationMock->expects($this->once())
            ->method('getStoreCode')
            ->willReturn($storeCode);
        $collectionMock = $this->createMock(Collection::class);
        $collectionMock->expects($this->once())
            ->method('setStore')
            ->with($storeCode)
            ->willReturnSelf();

        $this->assertSame($collectionMock, $this->storeModifier->modify($collectionMock, $feedSpecificationMock));
    }
}
