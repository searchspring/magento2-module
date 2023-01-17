<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\DataProvider;

use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManager;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\Stock\StockProviderInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\Stock\StockResolverInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\StockProvider;

class StockProviderTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        $this->stockResolverMock = $this->createMock(StockResolverInterface::class);
        $this->storeManagerMock = $this->createMock(StoreManager::class);
        $this->stockProvider = new StockProvider(
            $this->stockResolverMock,
            $this->storeManagerMock
        );
    }

    public function testGetData()
    {
        $storeMock = $this->createMock(Store::class);
        $providerMock = $this->createMock(StockProviderInterface::class);
        $feedSpecificationMock = $this->getMockForAbstractClass(FeedSpecificationInterface::class);
        $feedSpecificationMock->expects($this->once())
            ->method('getIgnoreFields')
            ->willReturn([]);
        $products = [
            [
                'entity_id' => 1
            ]
        ];
        $stockData = [
            1 => [
                'in_stock' => 1,
                'qty' => 333,
            ]
        ];
        $this->stockResolverMock->expects($this->once())
            ->method('resolve')
            ->willReturn($providerMock);
        $feedSpecificationMock->expects($this->once())
            ->method('getStoreCode')
            ->willReturn('default');

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn(1);
        $providerMock->expects($this->once())
            ->method('getStock')
            ->with([1], 1)
            ->willReturn($stockData);

        $this->assertSame(
            [array_merge(
                $products[0],
                [
                    'in_stock' => 1,
                    'stock_qty' => 333.0
                ]
            )],
            $this->stockProvider->getData($products, $feedSpecificationMock)
        );
    }
}
