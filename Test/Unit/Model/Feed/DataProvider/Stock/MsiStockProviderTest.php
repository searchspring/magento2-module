<?php
/**
 * Copyright (C) 2023 Searchspring <https://searchspring.com>
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace SearchSpring\Feed\Test\Unit\Model\Feed\DataProvider\Stock;

use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\ResourceModel\Product;
use Magento\CatalogInventory\Api\Data\StockItemCollectionInterface;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Api\StockItemCriteriaInterface;
use Magento\CatalogInventory\Api\StockItemCriteriaInterfaceFactory;
use Magento\CatalogInventory\Api\StockItemRepositoryInterface;
use Magento\CatalogInventory\Model\Stock\Item;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\InventoryApi\Api\Data\StockInterface;
use Magento\InventoryReservationsApi\Model\GetReservationsQuantityInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface as MsiStockResolverInterface;
use Magento\InventorySalesApi\Model\GetStockItemDataInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\Stock\MsiStockProvider;

class MsiStockProviderTest extends \PHPUnit\Framework\TestCase
{
    private $storeManagerMock;

    private $websiteRepositoryMock;

    private $productResourceMock;

    private $legacyStockItemCriteriaFactoryMock;

    private $legacyStockItemRepositoryMock;

    private $stockConfigurationMock;

    private $typeManagerMock;

    /**
     * @var GetReservationsQuantityInterface
     */
    private $getReservationsQuantityMock;

    /**
     * @var MsiStockResolverInterface
     */
    private $stockResolverMock;

    /**
     * @var GetStockItemDataInterface
     */
    private $getStockItemDataMock;

    private $loggerMock;

    private $msiStockProvider;

    public function setUp(): void
    {
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->websiteRepositoryMock = $this->createMock(WebsiteRepositoryInterface::class);
        $this->productResourceMock = $this->createMock(Product::class);
        $this->legacyStockItemCriteriaFactoryMock = $this->createMock(StockItemCriteriaInterfaceFactory::class);
        $this->legacyStockItemRepositoryMock = $this->createMock(StockItemRepositoryInterface::class);
        $this->stockConfigurationMock = $this->createMock(StockConfigurationInterface::class);
        $this->typeManagerMock = $this->createMock(Type::class);
        $this->getReservationsQuantityMock = $this->createMock(GetReservationsQuantityInterface::class);
        $this->stockResolverMock = $this->createMock(MsiStockResolverInterface::class);
        $this->getStockItemDataMock = $this->createMock(GetStockItemDataInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->msiStockProvider = new MsiStockProvider(
            $this->storeManagerMock,
            $this->websiteRepositoryMock,
            $this->productResourceMock,
            $this->legacyStockItemCriteriaFactoryMock,
            $this->legacyStockItemRepositoryMock,
            $this->stockConfigurationMock,
            $this->typeManagerMock,
            $this->getReservationsQuantityMock,
            $this->stockResolverMock,
            $this->getStockItemDataMock,
            $this->loggerMock
        );
    }

    public function testGetStock()
    {
        $reservationFirst = (float)rand(0, 10);
        $reservationSecond = (float)rand(0, 10);
        $reservationThird = (float)rand(0, 10);
        $getReservationsQuantityMock = $this->createMock(GetReservationsQuantityInterface::class);
        $stockResolverMock = $this->createMock(StockResolverInterface::class);
        $getStockItemDataMock = $this->createMock(GetStockItemDataInterface::class);
        $itemMock = $this->createMock(Item::class);
        $itemMockSecond = $this->createMock(Item::class);
        $itemMockThird = $this->createMock(Item::class);
        $itemsMock = [$itemMock, $itemMockSecond, $itemMockThird];
        $productIds = [1,2,3];
        $websiteMock = $this->getMockForAbstractClass(WebsiteInterface::class);
        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $stockMock = $this->createMock(StockInterface::class);
        $stockItemCollectionMock = $this->getMockForAbstractClass(StockItemCollectionInterface::class);
        $legacyStockInterfaceMock = $this->getMockForAbstractClass(StockItemCriteriaInterface::class);

        $msiStockProvider = new \ReflectionClass(MsiStockProvider::class);
        $getReservationsQuantity = $msiStockProvider->getProperty('getReservationsQuantity');
        $getReservationsQuantity->setAccessible(true);
        $getReservationsQuantity->setValue($this->msiStockProvider, $getReservationsQuantityMock);
        $stockResolver = $msiStockProvider->getProperty('stockResolver');
        $stockResolver->setAccessible(true);
        $stockResolver->setValue($this->msiStockProvider, $stockResolverMock);
        $getStockItemData = $msiStockProvider->getProperty('getStockItemData');
        $getStockItemData->setAccessible(true);
        $getStockItemData->setValue($this->msiStockProvider, $getStockItemDataMock);

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
        $stockResolverMock->expects($this->once())
            ->method('execute')
            ->with(SalesChannelInterface::TYPE_WEBSITE, 'default')
            ->willReturn($stockMock);
        $stockMock->expects($this->once())
            ->method('getStockId')
            ->willReturn(1);
        $this->productResourceMock->expects($this->once())
            ->method('getProductsSku')
            ->with($productIds)
            ->willReturn([
                [
                    'entity_id' => 1,
                    'sku' => '1',
                ],
                [
                    'entity_id' => 2,
                    'sku' => '2',
                ],
                [
                    'entity_id' => 3,
                    'sku' => '3',
                ],
            ]);

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
        $itemMockSecond->expects($this->once())
            ->method('getProductId')
            ->willReturn(2);
        $itemMockThird->expects($this->once())
            ->method('getProductId')
            ->willReturn(3);

        $getStockItemDataMock->expects($this->at(0))
            ->method('execute')
            ->with('1', 1)
            ->willReturn([
                'quantity' => 1,
                'is_salable' => true
            ]);
        $getStockItemDataMock->expects($this->at(1))
            ->method('execute')
            ->with('2', 1)
            ->willReturn([
                'quantity' => 2,
                'is_salable' => true
            ]);
        $getStockItemDataMock->expects($this->at(2))
            ->method('execute')
            ->with('3', 1)
            ->willReturn([
                'quantity' => 3,
                'is_salable' => true
            ]);

        $getReservationsQuantityMock->expects($this->at(0))
            ->method('execute')
            ->with('1', 1)
            ->willReturn($reservationFirst);
        $itemMock->expects($this->once())
            ->method('setStoreId')
            ->with(1);
        $itemMock->expects($this->any())
            ->method('getManageStock')
            ->willReturn(false);
        $getReservationsQuantityMock->expects($this->at(1))
            ->method('execute')
            ->with('2', 1)
            ->willReturn($reservationSecond);
        $itemMockSecond->expects($this->once())
            ->method('setStoreId')
            ->with(1);
        $itemMockSecond->expects($this->any())
            ->method('getManageStock')
            ->willReturn(true);
        $itemMockSecond->expects($this->once())
            ->method('__call')
            ->with('getTypeId')
            ->willReturn('configurable');
        $this->typeManagerMock->expects($this->any())
            ->method('getCompositeTypes')
            ->willReturn(['configurable', 'bundle']);
        $getReservationsQuantityMock->expects($this->at(2))
            ->method('execute')
            ->with('3', 1)
            ->willReturn($reservationThird);
        $itemMockThird->expects($this->once())
            ->method('setStoreId')
            ->with(1);
        $itemMockThird->expects($this->any())
            ->method('getManageStock')
            ->willReturn(true);
        $itemMockThird->expects($this->once())
            ->method('__call')
            ->with('getTypeId')
            ->willReturn('simple');
        $itemMockThird->expects($this->once())
            ->method('getMinQty')
            ->willReturn(13);

        $this->assertSame(
            [
                1 => [
                    'qty' => 1 + $reservationFirst,
                    'in_stock' => true,
                    'is_stock_managed' => false
                ],
                2 => [
                    'qty' => 2 + $reservationSecond,
                    'in_stock' => true,
                    'is_stock_managed' =>true
                ],
                3 => [
                    'qty' => 3 + $reservationThird,
                    'in_stock' => false,
                    'is_stock_managed' =>true
                ]
            ],
            $this->msiStockProvider->getStock($productIds, 1)
        );
    }

    public function testGetStockExceptionCase()
    {
        $getReservationsQuantityMock = $this->createMock(GetReservationsQuantityInterface::class);
        $stockResolverMock = $this->createMock(StockResolverInterface::class);
        $getStockItemDataMock = $this->createMock(GetStockItemDataInterface::class);
        $itemMock = $this->createMock(Item::class);
        $itemMockSecond = $this->createMock(Item::class);
        $itemMockThird = $this->createMock(Item::class);
        $itemsMock = [$itemMock, $itemMockSecond, $itemMockThird];
        $productIds = [1,2,3];
        $websiteMock = $this->getMockForAbstractClass(WebsiteInterface::class);
        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $stockMock = $this->createMock(StockInterface::class);
        $stockItemCollectionMock = $this->getMockForAbstractClass(StockItemCollectionInterface::class);
        $legacyStockInterfaceMock = $this->getMockForAbstractClass(StockItemCriteriaInterface::class);

        $msiStockProvider = new \ReflectionClass(MsiStockProvider::class);
        $getReservationsQuantity = $msiStockProvider->getProperty('getReservationsQuantity');
        $getReservationsQuantity->setAccessible(true);
        $getReservationsQuantity->setValue($this->msiStockProvider, $getReservationsQuantityMock);
        $stockResolver = $msiStockProvider->getProperty('stockResolver');
        $stockResolver->setAccessible(true);
        $stockResolver->setValue($this->msiStockProvider, $stockResolverMock);
        $getStockItemData = $msiStockProvider->getProperty('getStockItemData');
        $getStockItemData->setAccessible(true);
        $getStockItemData->setValue($this->msiStockProvider, $getStockItemDataMock);

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
        $stockResolverMock->expects($this->once())
            ->method('execute')
            ->with(SalesChannelInterface::TYPE_WEBSITE, 'default')
            ->willReturn($stockMock);
        $stockMock->expects($this->once())
            ->method('getStockId')
            ->willReturn(1);
        $this->productResourceMock->expects($this->once())
            ->method('getProductsSku')
            ->with($productIds)
            ->willReturn([
                [
                    'entity_id' => 1,
                    'sku' => '1',
                ],
                [
                    'entity_id' => 2,
                    'sku' => '2',
                ],
                [
                    'entity_id' => 3,
                    'sku' => '3',
                ],
            ]);

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
        $itemMockSecond->expects($this->once())
            ->method('getProductId')
            ->willReturn(2);
        $itemMockThird->expects($this->once())
            ->method('getProductId')
            ->willReturn(3);

        $getStockItemDataMock->expects($this->any())
            ->method('execute')
            ->with('1', 1)
            ->willThrowException(new \Exception());

        $this->assertSame(
            [],
            $this->msiStockProvider->getStock($productIds, 1)
        );
    }

    public function testGetStockWithoutLegacyStockItemFallsBackToStoreConfig()
    {
        $productIds = [1, 2, 3, 4, 5];
        $storeMock = $this->createMock(StoreInterface::class);
        $connectionMock = $this->createMock(AdapterInterface::class);
        $selectMock = $this->createMock(Select::class);
        $websiteMock = $this->createMock(WebsiteInterface::class);
        $stockMock = $this->createMock(StockInterface::class);
        $criteriaMock = $this->createMock(StockItemCriteriaInterface::class);
        $stockItemCollectionMock = $this->createMock(StockItemCollectionInterface::class);

        $this->storeManagerMock->method('getStore')->with(1)->willReturn($storeMock);
        $storeMock->method('getWebsiteId')->willReturn(1);
        $this->websiteRepositoryMock->method('getById')->with(1)->willReturn($websiteMock);
        $websiteMock->method('getCode')->willReturn('default');
        $this->stockResolverMock->method('execute')
            ->with(SalesChannelInterface::TYPE_WEBSITE, 'default')
            ->willReturn($stockMock);
        $stockMock->method('getStockId')->willReturn(1);
        $this->productResourceMock->method('getProductsSku')->with($productIds)->willReturn([
            ['entity_id' => 1, 'sku' => '1'],
            ['entity_id' => 2, 'sku' => '2'],
            ['entity_id' => 3, 'sku' => '3'],
            ['entity_id' => 4, 'sku' => '4'],
            ['entity_id' => 5, 'sku' => '5'],
        ]);
        $this->productResourceMock->method('getConnection')->willReturn($connectionMock);
        $this->productResourceMock->method('getTable')->willReturn('catalog_product_entity');
        $connectionMock->method('select')->willReturn($selectMock);
        $selectMock->method('from')->willReturnSelf();
        $selectMock->method('where')->willReturnSelf();
        $connectionMock->expects($this->once())->method('fetchPairs')->with($selectMock)->willReturn([
            1 => 'simple',
            2 => 'simple',
            3 => 'simple',
            4 => 'simple',
            5 => 'configurable',
        ]);
        $this->legacyStockItemCriteriaFactoryMock->method('create')->willReturn($criteriaMock);
        $criteriaMock->method('setScopeFilter')->willReturnSelf();
        $criteriaMock->method('setProductsFilter')->willReturnSelf();
        $this->legacyStockItemRepositoryMock->method('getList')->willReturn($stockItemCollectionMock);
        // none of the products has a cataloginventory_stock_item row
        $stockItemCollectionMock->method('getItems')->willReturn([]);

        $this->getStockItemDataMock->method('execute')->willReturnMap([
            ['1', 1, ['quantity' => 5, 'is_salable' => true]],
            ['2', 1, ['quantity' => 0, 'is_salable' => false]],
            ['3', 1, null],
            // indexed qty says salable, but a pending order reservation consumes all of it
            ['4', 1, ['quantity' => 5, 'is_salable' => true]],
            // composite products have no own qty, so the salable flag decides
            ['5', 1, ['quantity' => 0, 'is_salable' => true]],
        ]);
        $this->getReservationsQuantityMock->method('execute')->willReturnMap([
            ['1', 1, 0.0],
            ['2', 1, 0.0],
            ['3', 1, 0.0],
            ['4', 1, -5.0],
            ['5', 1, 0.0],
        ]);
        $this->typeManagerMock->method('getCompositeTypes')->willReturn(['configurable', 'bundle', 'grouped']);

        $this->stockConfigurationMock->method('getManageStock')->with(1)->willReturn(true);
        $this->stockConfigurationMock->method('getMinQty')->with(1)->willReturn(0.0);

        $this->loggerMock->expects($this->once())
            ->method('warning')
            ->with($this->anything(), $this->callback(static function (array $context): bool {
                return $context['productIds'] === [1, 2, 3, 4, 5];
            }));

        $this->assertSame(
            [
                1 => ['qty' => 5.0, 'in_stock' => true, 'is_stock_managed' => true],
                2 => ['qty' => 0.0, 'in_stock' => false, 'is_stock_managed' => true],
                3 => ['qty' => 0.0, 'in_stock' => false, 'is_stock_managed' => true],
                4 => ['qty' => 0.0, 'in_stock' => false, 'is_stock_managed' => true],
                5 => ['qty' => 0.0, 'in_stock' => true, 'is_stock_managed' => true],
            ],
            $this->msiStockProvider->getStock($productIds, 1)
        );
    }
}
