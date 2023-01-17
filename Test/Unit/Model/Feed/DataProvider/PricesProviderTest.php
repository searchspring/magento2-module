<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\DataProvider;

use Magento\Catalog\Model\Product;
use Magento\Framework\Serialize\Serializer\Json;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\Price\PriceProviderInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\Price\ProviderResolverInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\PricesProvider;

class PricesProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Json
     */
    private $jsonMock;

    /**
     * @var ProviderResolverInterface
     */
    private $priceProviderResolverMock;

    private $pricesProvider;

    public function setUp(): void
    {
        $this->jsonMock = $this->createMock(Json::class);
        $this->priceProviderResolverMock = $this->createMock(ProviderResolverInterface::class);
        $this->pricesProvider = new PricesProvider(
            $this->jsonMock,
            $this->priceProviderResolverMock
        );
    }

    public function testGetData()
    {
        $priceProviderMock = $this->createMock(PriceProviderInterface::class);
        $feedSpecificationMock = $this->getMockForAbstractClass(FeedSpecificationInterface::class);
        $productMock = $this->createMock(Product::class);
        $products = [
            [
                'product_model' => $productMock
            ]
        ];
        $tierPrice = ['test' => 2.33];
        $prices = ['regular_price' => 1.0, 'final_price' => 3.33, 'max_price' => 3.33];
        $feedSpecificationMock->expects($this->once())
            ->method('getIgnoreFields')
            ->willReturn([]);
        $this->priceProviderResolverMock->expects($this->once())
            ->method('resolve')
            ->with($productMock)
            ->willReturn($priceProviderMock);
        $priceProviderMock->expects($this->once())
            ->method('getPrices')
            ->with($productMock, [])
            ->willReturn($prices);
        $feedSpecificationMock->expects($this->once())
            ->method('getIncludeTierPricing')
            ->willReturn(true);
        $productMock->expects($this->once())
            ->method('getTierPrice')
            ->willReturn($tierPrice);
        $this->jsonMock->expects($this->once())
            ->method('serialize')
            ->with($tierPrice)
            ->willReturn(json_encode($tierPrice));

        $this->assertSame(
            [array_merge($products[0], array_merge($prices, ['tier_pricing' => json_encode($tierPrice)]))],
            $this->pricesProvider->getData($products, $feedSpecificationMock)
        );
    }
}
