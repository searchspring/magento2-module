<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\Collection;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\Collection\TierPriceProcessor;

class TierPriceProcessorTest extends \PHPUnit\Framework\TestCase
{
    private $tierPriceProcessor;

    public function setUp(): void
    {
        $this->tierPriceProcessor = new TierPriceProcessor();
    }

    public function testProcessAfterLoad()
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

    public function testProcessAfterFetchItems()
    {
        $collectionMock = $this->createMock(Collection::class);
        $feedSpecificationMock = $this->createMock(FeedSpecificationInterface::class);
        $feedSpecificationMock->expects($this->once())
            ->method('getIncludeTierPricing')
            ->willReturn(true);
        $collectionMock->expects($this->once())
            ->method('setFlag')
            ->with('tier_price_added', false);

        $this->assertSame(
            $collectionMock,
            $this->tierPriceProcessor->processAfterFetchItems($collectionMock, $feedSpecificationMock)
        );
    }
}
