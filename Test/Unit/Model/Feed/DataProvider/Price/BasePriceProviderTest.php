<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\DataProvider\Price;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use SearchSpring\Feed\Model\Feed\DataProvider\Price\BasePriceProvider;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Catalog\Pricing\Price\RegularPrice;
use SearchSpring\Feed\Model\Feed\DataProvider\PricesProvider;
use Magento\Framework\Pricing\PriceInfoInterface;
use Magento\Framework\Pricing\Price\PriceInterface;

class BasePriceProviderTest extends \PHPUnit\Framework\TestCase
{
    private $typeMock;

    private $basePriceProvider;

    public function setUp(): void
    {
        $this->typeMock = $this->createMock(Type::class);
        $this->basePriceProvider = new BasePriceProvider($this->typeMock);
    }

    public function testGetPrices()
    {
        $priceInterfaceMock = $this->getMockForAbstractClass(PriceInterface::class);
        $regularPriceMock = $this->createMock(RegularPrice::class);
        $finalPriceMock = $this->createMock(FinalPrice::class);
        $priceInfoInterfaceMock = $this->getMockForAbstractClass(PriceInfoInterface::class);
        $productMock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $productMock->expects($this->any())
            ->method('getPriceInfo')
            ->willReturn($priceInfoInterfaceMock);
        $priceInfoInterfaceMock->expects($this->at(0))
            ->method('getPrice')
            ->with(FinalPrice::PRICE_CODE)
            ->willReturn($finalPriceMock);
        $priceInfoInterfaceMock->expects($this->at(1))
            ->method('getPrice')
            ->with(RegularPrice::PRICE_CODE)
            ->willReturn($regularPriceMock);
        $priceInfoInterfaceMock->expects($this->at(2))
            ->method('getPrice')
            ->with(FinalPrice::PRICE_CODE)
            ->willReturn($finalPriceMock);
        $finalPriceMock->expects($this->once())
            ->method('getMinimalPrice')
            ->willReturn($priceInterfaceMock);
        $finalPriceMock->expects($this->once())
            ->method('getMaximalPrice')
            ->willReturn($priceInterfaceMock);

        $regularPriceMock->expects($this->once())
            ->method('getValue')
            ->willReturn(0.5);
        $priceInterfaceMock->expects($this->any())
            ->method('getValue')
            ->willReturn(1.0);

        $this->assertSame(
            [
                FinalPrice::PRICE_CODE => 1.0,
                RegularPrice::PRICE_CODE => 0.5,
                PricesProvider::MAX_PRICE_KEY => 1.0
            ],
            $this->basePriceProvider->getPrices($productMock, [])
        );
    }
}
