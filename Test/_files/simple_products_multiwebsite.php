<?php

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Action;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

require __DIR__ . '/../../../../../../dev/tests/integration/testsuite/Magento/Store/_files/second_website_with_store_group_and_store.php';
require __DIR__ . '/simple_products.php';
$objectManager = Bootstrap::getObjectManager();
/** @var StoreManagerInterface $storeManager */
$storeManager = $objectManager->get(StoreManagerInterface::class);
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(ProductRepositoryInterface::class);
/** @var Magento\Store\Model\Store $store */
$store = $storeManager->getStore('fixture_second_store');
$storeId = $store->getId();
$websiteId = $store->getWebsiteId();
/** @var Action $productAction */
$productAction = $objectManager->create(
    Action::class
);
/** @var Product $product1 */
$product1 = $productRepository->get('searchspring_simple_1');
/** @var Product $product2 */
$product2 = $productRepository->get('searchspring_simple_2');
$productAction->updateWebsites([$product2->getId()], [$websiteId], 'add');
$productAction->updateWebsites([$product1->getId()], [$websiteId], 'add');
/** @var Product $product1 */
$product1 = $productRepository->get('searchspring_simple_1', true, (int) $storeId, true);
/** @var Product $product2 */
$product2 = $productRepository->get('searchspring_simple_2', true, (int) $storeId, true);
$product1->setStoreId($storeId)
    ->setPrice(20);
$product1->save();
$product2->setStoreId($storeId)
    ->setPrice(20);
$product2->save();

$objectManager->get(IndexerRegistry::class)
    ->get('catalog_product_price')
    ->reindexAll();
