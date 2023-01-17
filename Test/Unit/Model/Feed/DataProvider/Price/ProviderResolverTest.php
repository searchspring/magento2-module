<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\DataProvider\Price;

use Magento\Catalog\Model\Product;
use SearchSpring\Feed\Model\Feed\DataProvider\Price\BasePriceProvider;
use SearchSpring\Feed\Model\Feed\DataProvider\Price\ProviderResolver;

class ProviderResolverTest extends \PHPUnit\Framework\TestCase
{
    private $basePriceProviderMock;

    private $providerResolver;

    public function setUp(): void
    {
        $this->basePriceProviderMock = $this->createMock(BasePriceProvider::class);
        $this->providerResolver = new ProviderResolver($this->basePriceProviderMock);
    }

    public function testResolve()
    {
        $productMock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $productMock->expects($this->once())
            ->method('getTypeId')
            ->willReturn('simple');

        $this->assertSame(
            $this->basePriceProviderMock,
            $this->providerResolver->resolve($productMock)
        );
    }
}
