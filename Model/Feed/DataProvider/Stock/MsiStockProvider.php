<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider\Stock;

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
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * MsiStockProvider constructor.
     * @param StoreManagerInterface $storeManager
     * @param WebsiteRepositoryInterface $websiteRepository
     * @param Product $productResource
     * @param StockItemCriteriaInterfaceFactory $legacyStockItemCriteriaFactory
     * @param StockItemRepositoryInterface $legacyStockItemRepository
     * @param StockConfigurationInterface $stockConfiguration
     * @param ObjectManager $objectManager
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        WebsiteRepositoryInterface $websiteRepository,
        Product $productResource,
        StockItemCriteriaInterfaceFactory $legacyStockItemCriteriaFactory,
        StockItemRepositoryInterface $legacyStockItemRepository,
        StockConfigurationInterface $stockConfiguration,
        ObjectManager $objectManager
    ) {
        $this->storeManager = $storeManager;
        $this->websiteRepository = $websiteRepository;
        $this->productResource = $productResource;
        $this->legacyStockItemCriteriaFactory = $legacyStockItemCriteriaFactory;
        $this->legacyStockItemRepository = $legacyStockItemRepository;
        $this->stockConfiguration = $stockConfiguration;
        $this->objectManager = $objectManager;
    }

    /**
     * [
     *      product_id => [
     *          'qty' => float,
     *          'in_stock' => bool
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

        $this->init();
        $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
        $website = $this->websiteRepository->getById($websiteId);
        $stock = $this->stockResolver->execute(SalesChannelInterface::TYPE_WEBSITE, $website->getCode());
        $stockId = $stock->getStockId();
        $skus = $this->getSkus($productIds);
        $configurations = $this->getItemConfigurations($productIds);
        $result = [];
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
                continue;
            }

            /** @var Item $configuration */
            $configuration = $configurations[$productId] ?? null;
            $configuration->setStoreId($storeId);
            $result[$productId] = [
                'qty' => $this->getQty($stockData, $reservation),
                'in_stock' => $this->getIsInStock($stockData, $reservation, $configuration)
            ];
        }

        return $result;
    }

    /**
     *
     */
    private function init() : void
    {
        // we cannot use constructor because MSI module codebase can be removed via composer
        $this->getReservationsQuantity = $this->objectManager->get('Magento\InventoryReservationsApi\Model\GetReservationsQuantityInterface');
        $this->stockResolver = $this->objectManager->get('Magento\InventorySalesApi\Api\StockResolverInterface');
        $this->getStockItemData = $this->objectManager->get('Magento\InventorySalesApi\Model\GetStockItemDataInterface');
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
     * @return bool
     */
    private function getIsInStock(array $stockData, float $reservation, StockItemInterface $configuration = null) : bool
    {
        if (!$configuration) {
            return false;
        }

        if (!$configuration->getManageStock()) {
            return true;
        }

        return $this->getQty($stockData, $reservation) > $configuration->getMinQty();
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
