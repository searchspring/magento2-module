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

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider\Stock;

use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\ResourceModel\Product;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Api\StockItemCriteriaInterfaceFactory;
use Magento\CatalogInventory\Api\StockItemRepositoryInterface;
use Magento\CatalogInventory\Model\Stock\Item;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryReservationsApi\Model\GetReservationsQuantityInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface as MsiStockResolverInterface;
use Magento\InventorySalesApi\Model\GetStockItemDataInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Filesystem\DirectoryList;
use Psr\Log\LoggerInterface;
class MsiStockProvider implements StockProviderInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var WebsiteRepositoryInterface
     */
    private $websiteRepository;
    /**
     * @var MsiStockResolverInterface
     */
    private $stockResolver;
    /**
     * @var GetStockItemDataInterface
     */
    private $getStockItemData;
    /**
     * @var Product
     */
    private $productResource;
    /**
     * @var GetReservationsQuantityInterface
     */
    private $getReservationsQuantity;
    /**
     * @var StockItemCriteriaInterfaceFactory
     */
    private $legacyStockItemCriteriaFactory;
    /**
     * @var StockItemRepositoryInterface
     */
    private $legacyStockItemRepository;
    /**
     * @var StockConfigurationInterface
     */
    private $stockConfiguration;
    /**
     * @var Type
     */
    private $typeManager;
    /**
     * @var LoggerInterface
     */
    private $logger;


    /**
     * MsiStockProvider constructor.
     * @param StoreManagerInterface $storeManager
     * @param WebsiteRepositoryInterface $websiteRepository
     * @param Product $productResource
     * @param StockItemCriteriaInterfaceFactory $legacyStockItemCriteriaFactory
     * @param StockItemRepositoryInterface $legacyStockItemRepository
     * @param StockConfigurationInterface $stockConfiguration
     * @param Type $typeManager
     * @param GetReservationsQuantityInterface $getReservationsQuantity
     * @param MsiStockResolverInterface $stockResolver
     * @param GetStockItemDataInterface $getStockItemData
     * @param LoggerInterface $logger
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        WebsiteRepositoryInterface $websiteRepository,
        Product $productResource,
        StockItemCriteriaInterfaceFactory $legacyStockItemCriteriaFactory,
        StockItemRepositoryInterface $legacyStockItemRepository,
        StockConfigurationInterface $stockConfiguration,
        Type $typeManager,
        GetReservationsQuantityInterface $getReservationsQuantity,
        MsiStockResolverInterface $stockResolver,
        GetStockItemDataInterface  $getStockItemData,
        LoggerInterface $logger,
    ) {
        $this->storeManager = $storeManager;
        $this->websiteRepository = $websiteRepository;
        $this->productResource = $productResource;
        $this->legacyStockItemCriteriaFactory = $legacyStockItemCriteriaFactory;
        $this->legacyStockItemRepository = $legacyStockItemRepository;
        $this->stockConfiguration = $stockConfiguration;
        $this->typeManager = $typeManager;
        $this->getReservationsQuantity =  $getReservationsQuantity;
        $this->stockResolver =  $stockResolver;
        $this->getStockItemData =  $getStockItemData;
        $this->logger =  $logger;
    }

    /**
     * [
     *      product_id => [
     *          'qty' => float,
     *          'in_stock' => bool,
     *          'is_stock_managed' =>bool
     *      ],
     *      .........
     * ]
     *
     * @param array $productIds
     * @param int $storeId
     * @return array
     * @throws NoSuchEntityException
     */
    public function getStock(array $productIds, int $storeId): array
    {
        if (empty($productIds)) {
            return [];
        }

        $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
        $website = $this->websiteRepository->getById($websiteId);
        $stock = $this->stockResolver->execute(SalesChannelInterface::TYPE_WEBSITE, $website->getCode());
        $stockId = $stock->getStockId();
        $skus = $this->getSkus($productIds);
        $configurations = $this->getItemConfigurations($productIds);
        $result = [];
        $missingConfigurationIds = [];
        foreach ($productIds as $productId) {
            $sku = $skus[$productId] ?? null;
            if (!$sku) {
                continue;
            }

            $sku = (string) $sku;
            try {
                $stockData = $this->getStockItemData->execute($sku, $stockId) ?? [];
                $reservation = $this->getReservationsQuantity->execute($sku, $stockId);
            } catch (\Exception $exception) {
                $this->logger->error(
                    "Error processing stock data for SKU: {$sku}",
                    ['exception' => $exception]
                );
                continue;
            }

            /** @var Item|null $configuration */
            $configuration = $configurations[$productId] ?? null;
            if ($configuration) {
                $configuration->setStoreId($storeId);
                $isStockManaged = $configuration->getManageStock();
            } else {
                // No cataloginventory_stock_item row (e.g. products created via MSI-only import or direct SQL).
                // Fall back to the store level stock configuration instead of failing the whole batch.
                $missingConfigurationIds[] = $productId;
                $isStockManaged = (bool) $this->stockConfiguration->getManageStock($storeId);
            }
            $result[$productId] = [
                'qty' => $this->getQty($stockData, $reservation),
                'in_stock' => $this->getIsInStock($stockData, $reservation, $configuration, $storeId),
                'is_stock_managed' => $isStockManaged
            ];
        }

        if ($missingConfigurationIds) {
            $this->logger->warning(
                'Products have no cataloginventory_stock_item row; using store stock configuration defaults',
                [
                    'storeId' => $storeId,
                    'stockId' => $stockId,
                    'productIds' => $missingConfigurationIds,
                ]
            );
        }

        return $result;
    }

    /**
     * @param array $stockData
     * @param float $reservation
     * @return float
     */
    private function getQty(array $stockData, float $reservation) : float
    {
        if (!isset($stockData[GetStockItemDataInterface::QUANTITY])) {
            return 0;
        }

        return $stockData[GetStockItemDataInterface::QUANTITY] + $reservation;
    }

    /**
     * @param array $stockData
     * @param float $reservation
     * @param StockItemInterface|null $configuration
     * @param int|null $storeId
     * @return bool
     */
    private function getIsInStock(
        array $stockData,
        float $reservation,
        ?StockItemInterface $configuration = null,
        ?int $storeId = null
    ) : bool {
        if (!$configuration) {
            return $this->getIsInStockFromStoreConfig($stockData, $reservation, $storeId);
        }

        if (!$configuration->getManageStock()) {
            return true;
        }

        $isSalable = $stockData['is_salable'] ?? null;
        // composite products (configurable, grouped, bundle) always have 0 qty
        if (!is_null($isSalable) && in_array($configuration->getTypeId(),  $this->typeManager->getCompositeTypes())) {
            return (bool) $isSalable;
        }

        if (!is_null($isSalable) && $isSalable == 0) {
            return false;
        }

        return $this->getQty($stockData, $reservation) > $configuration->getMinQty();
    }

    /**
     * Determine stock status for a product without a legacy stock item, using store level configuration
     *
     * @param array $stockData
     * @param float $reservation
     * @param int|null $storeId
     * @return bool
     */
    private function getIsInStockFromStoreConfig(array $stockData, float $reservation, ?int $storeId) : bool
    {
        if (!$this->stockConfiguration->getManageStock($storeId)) {
            return true;
        }

        // product type is unknown here, so trust the MSI salable flag when it is available
        if (isset($stockData['is_salable'])) {
            return (bool) $stockData['is_salable'];
        }

        return $this->getQty($stockData, $reservation) > (float) $this->stockConfiguration->getMinQty($storeId);
    }

    /**
     * @param array $productIds
     * @return StockItemInterface[]
     */
    private function getItemConfigurations(array $productIds) : array
    {
        $searchCriteria = $this->legacyStockItemCriteriaFactory->create();
        $searchCriteria->setScopeFilter($this->stockConfiguration->getDefaultScopeId());
        $searchCriteria->setProductsFilter($productIds);
        $items = $this->legacyStockItemRepository->getList($searchCriteria)->getItems();
        $result = [];
        foreach ($items as $item) {
            $result[$item->getProductId()] = $item;
        }

        return $result;
    }

    /**
     * @param array $productIds
     * @return array
     */
    private function getSkus(array $productIds) : array
    {
        $skus = $this->productResource->getProductsSku($productIds);
        $result = [];
        foreach ($skus as $skuData) {
            $result[$skuData['entity_id']] = $skuData['sku'];
        }

        return $result;
    }
}
