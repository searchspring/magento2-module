<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\DataProvider\Stock;

use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Api\StockItemCriteriaInterface;
use Magento\CatalogInventory\Api\StockItemCriteriaInterfaceFactory;
use Magento\CatalogInventory\Api\StockItemRepositoryInterface;
use Magento\CatalogInventory\Model\Stock\Item;
use SearchSpring\Feed\Model\Feed\DataProvider\Stock\LegacyStockProvider;
use Magento\CatalogInventory\Api\Data\StockItemCollectionInterface;

class LegacyStockProviderTest extends \PHPUnit\Framework\TestCase
{
    private $legacyStockItemCriteriaFactoryMock;

    private $legacyStockItemRepositoryMock;

    private $stockConfigurationMock;

    private $legacyStockProvider;

    public function setUp(): void
    {
        $this->legacyStockItemCriteriaFactoryMock = $this->createMock(StockItemCriteriaInterfaceFactory::class);
        $this->legacyStockItemRepositoryMock = $this->createMock(StockItemRepositoryInterface::class);
        $this->stockConfigurationMock = $this->createMock(StockConfigurationInterface::class);
        $this->legacyStockProvider = new LegacyStockProvider(
            $this->legacyStockItemCriteriaFactoryMock,
            $this->legacyStockItemRepositoryMock,
            $this->stockConfigurationMock
        );
    }

    public function testGetStock()
    {
        $itemMock = $this->createMock(Item::class);
        $itemsMock = [$itemMock];
        $stockItemCollectionMock = $this->getMockForAbstractClass(StockItemCollectionInterface::class);
        $productIds = [1,2,3];
        $this->stockConfigurationMock->expects($this->once())
            ->method('getDefaultScopeId')
            ->willReturn(0);
        $legacyStockInterfaceMock = $this->getMockForAbstractClass(StockItemCriteriaInterface::class);
        $this->legacyStockItemCriteriaFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($legacyStockInterfaceMock);
        $legacyStockInterfaceMock->expects($this->once())
            ->method('setScopeFilter')
            ->willReturnSelf();
        $legacyStockInterfaceMock->expects($this->once())
            ->method('setProductsFilter')
            ->with($productIds)
            ->willReturnSelf();
        $this->legacyStockItemRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($legacyStockInterfaceMock)
            ->willReturn($stockItemCollectionMock);
        $stockItemCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn($itemsMock);

        $itemMock->expects($this->once())
            ->method('setStoreId')
            ->with(0)
            ->willReturnSelf();
        $itemMock->expects($this->once())
            ->method('getProductId')
            ->willReturn(1);
        $itemMock->expects($this->once())
            ->method('getQty')
            ->willReturn(3);
        $itemMock->expects($this->once())
            ->method('getIsInStock')
            ->willReturn(true);

        $this->assertSame(
            [
                1 => [
                    'qty' => 3,
                    'in_stock' => true
                ]
            ],
            $this->legacyStockProvider->getStock($productIds, 0)
        );
    }
}
