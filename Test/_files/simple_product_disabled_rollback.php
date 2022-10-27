<?php
/**
 *  @author Dmitry Kisten <dkisten@absoluteweb.com>
 *  @author Absolute Web Services <info@absoluteweb.com>
 *  @copyright Copyright (c) 2021, Focus Camera, Inc.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Api\StockStatusCriteriaInterfaceFactory;
use Magento\CatalogInventory\Api\StockStatusRepositoryInterface;
use Magento\CatalogInventory\Model\StockRegistryStorage;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(ProductRepositoryInterface::class);
/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);
/** @var StockStatusRepositoryInterface $stockStatusRepository */
$stockStatusRepository = $objectManager->create(StockStatusRepositoryInterface::class);
/** @var StockStatusCriteriaInterfaceFactory $stockStatusCriteriaFactory */
$stockStatusCriteriaFactory = $objectManager->create(StockStatusCriteriaInterfaceFactory::class);
$currentArea = $registry->registry('isSecureArea');
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);
$productIds = [];

try {
    $product = $productRepository->get('searchspring_simple_disabled', false, null, true);
    $productRepository->delete($product);
    $productIds[] = $product->getId();
    $criteria = $stockStatusCriteriaFactory->create();
    $criteria->setProductsFilter($product->getId());

    $result = $stockStatusRepository->getList($criteria);
    if ($result->getTotalCount()) {
        $stockStatus = current($result->getItems());
        $stockStatusRepository->delete($stockStatus);
    }
} catch (NoSuchEntityException $exception) {
    //Product already removed
}

/** @var StockRegistryStorage $stockRegistryStorage */
$stockRegistryStorage = Bootstrap::getObjectManager()
    ->get(StockRegistryStorage::class);
foreach ($productIds as $productId) {
    $stockRegistryStorage->removeStockItem($productId);
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', $currentArea);
