<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\DataProvider\Product;

use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\Attribute\ValueProcessor;
use SearchSpring\Feed\Model\Feed\DataProvider\Product\GetChildProductsData;

class GetChildProductsDataTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        $this->valueProcessorMock = $this->createMock(ValueProcessor::class);
        $this->getChildProductsData = new GetChildProductsData($this->valueProcessorMock);
    }

    public function testGetProductData()
    {
        $feedSpecificationMock = $this->getMockForAbstractClass(FeedSpecificationInterface::class);
        $feedSpecificationMock->expects($this->once())
            ->method('getIgnoreFields')
            ->willReturn([]);

        $this->assertSame(
            [],
            $this->getChildProductsData->getProductData([], [], [], $feedSpecificationMock)
        );
    }
}
