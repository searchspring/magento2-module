<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\DataProvider\Stock;

use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\ResourceModel\Product;
use Magento\CatalogInventory\Api\Data\StockItemCollectionInterface;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Api\StockItemCriteriaInterface;
use Magento\CatalogInventory\Api\StockItemCriteriaInterfaceFactory;
use Magento\CatalogInventory\Api\StockItemRepositoryInterface;
use Magento\CatalogInventory\Model\Stock\Item;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\Stock\MsiStockProvider;

class MsiStockProviderTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->websiteRepositoryMock = $this->createMock(WebsiteRepositoryInterface::class);
        $this->productResourceMock = $this->createMock(Product::class);
        $this->legacyStockItemCriteriaFactoryMock = $this->createMock(StockItemCriteriaInterfaceFactory::class);
        $this->legacyStockItemRepositoryMock = $this->createMock(StockItemRepositoryInterface::class);
        $this->stockConfigurationMock = $this->createMock(StockConfigurationInterface::class);
        $this->typeManagerMock = $this->createMock(Type::class);
        $this->msiStockProvider = new MsiStockProvider(
            $this->storeManagerMock,
            $this->websiteRepositoryMock,
            $this->productResourceMock,
            $this->legacyStockItemCriteriaFactoryMock,
            $this->legacyStockItemRepositoryMock,
            $this->stockConfigurationMock,
            $this->typeManagerMock
        );
    }

    public function testGetStock()
    {
        $itemMock = $this->createMock(Item::class);
        $itemsMock = [$itemMock];
        $productIds = [1,2,3];
        $websiteMock = $this->getMockForAbstractClass(WebsiteInterface::class);
        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);
        $storeMock->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn(1);
        $this->websiteRepositoryMock->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($websiteMock);
        $websiteMock->expects($this->once())
            ->method('getCode')
            ->willReturn('default');
        $this->productResourceMock->expects($this->once())
            ->method('getProductsSku')
            ->with($productIds)
            ->willReturn([
                [
                    'entity_id' => 1,
                    'sku' => '',
                ],
                [
                    'entity_id' => 2,
                    'sku' => '',
                ],
                [
                    'entity_id' => 3,
                    'sku' => '',
                ],
            ]);
        $stockItemCollectionMock = $this->getMockForAbstractClass(StockItemCollectionInterface::class);
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
            ->method('getProductId')
            ->willReturn(1);

        $this->assertSame([], $this->msiStockProvider->getStock($productIds, 1));
    }
}
