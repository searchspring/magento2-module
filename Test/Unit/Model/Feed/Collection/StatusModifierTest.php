<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\Collection;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\Collection\StatusModifier;

class StatusModifierTest extends \PHPUnit\Framework\TestCase
{
    private $statusMock;

    private $statusModifier;

    public function setUp(): void
    {
        $this->statusMock = $this->createMock(Status::class);
        $this->statusModifier = new StatusModifier($this->statusMock);
    }

    public function testModify()
    {
        $feedSpecificationMock = $this->getMockForAbstractClass(FeedSpecificationInterface::class);
        $visibilityResults = [1];
        $this->statusMock->expects($this->once())
            ->method('getVisibleStatusIds')
            ->willReturn($visibilityResults);
        $collectionMock = $this->createMock(Collection::class);
        $collectionMock->expects($this->once())
            ->method('addAttributeToSelect')
            ->with(ProductInterface::STATUS)
            ->willReturnSelf();
        $collectionMock->expects($this->once())
            ->method('addAttributeToFilter')
            ->with(ProductInterface::STATUS, ['in' => $visibilityResults])
            ->willReturnSelf();

        $this->assertSame($collectionMock, $this->statusModifier->modify($collectionMock, $feedSpecificationMock));
    }
}
