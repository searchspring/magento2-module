<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\Collection;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\Collection\TierPriceProcessor;

class TierPriceProcessorTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        $this->tierPriceProcessor = new TierPriceProcessor();
    }

    public function testProcess()
    {
        $feedSpecificationMock = $this->getMockForAbstractClass(FeedSpecificationInterface::class);
        $feedSpecificationMock->expects($this->once())
            ->method('getIncludeTierPricing')
            ->willReturn(true);
        $collectionMock = $this->createMock(Collection::class);
        $collectionMock->expects($this->once())
            ->method('addTierPriceData')
            ->willReturnSelf();

        $this->assertSame(
            $collectionMock,
            $this->tierPriceProcessor->processAfterLoad($collectionMock, $feedSpecificationMock)
        );
    }
}
