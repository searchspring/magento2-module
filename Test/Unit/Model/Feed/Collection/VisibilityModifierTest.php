<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\Collection;

use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\Collection\VisibilityModifier;

class VisibilityModifierTest extends \PHPUnit\Framework\TestCase
{
    private $visibilityMock;

    private $visibilityModifier;

    public function setUp(): void
    {
        $this->visibilityMock = $this->createMock(Visibility::class);
        $this->visibilityModifier = new VisibilityModifier($this->visibilityMock);
    }

    public function testModify()
    {
        $visibility = [2,4];
        $feedSpecificationMock = $this->getMockForAbstractClass(FeedSpecificationInterface::class);
        $this->visibilityMock->expects($this->once())
            ->method('getVisibleInSiteIds')
            ->willReturn($visibility);
        $collectionMock = $this->createMock(Collection::class);
        $collectionMock->expects($this->once())
            ->method('setVisibility')
            ->with($visibility)
            ->willReturnSelf();

        $this->assertSame($collectionMock, $this->visibilityModifier->modify($collectionMock, $feedSpecificationMock));
    }
}
