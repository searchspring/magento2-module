<?php

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

require __DIR__ . '/configurable_products.php';

$objectManager = Bootstrap::getObjectManager();
/** @var SearchCriteriaBuilder $searchCriteriaBuilder */
$searchCriteriaBuilder = $objectManager->get(SearchCriteriaBuilder::class);
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(ProductRepositoryInterface::class);
/** @var StoreManagerInterface $storeManager */
$storeManager = $objectManager->get(StoreManagerInterface::class);
$store = $storeManager->getStore('default');
$storeId = (int) $store->getId();
$searchCriteria = $searchCriteriaBuilder->addFilter('sku', 'searchspring_configurable_test_simple%', 'like')
    ->create();

foreach ($productRepository->getList($searchCriteria)->getItems() as $product) {
    $product = $productRepository->get($product->getSku());
    $product->setStoreId($storeId);
    $product->setName('Store Default ' . $product->getName());
    $productRepository->save($product);
}

