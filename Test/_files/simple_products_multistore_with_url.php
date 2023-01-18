<?php

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

require __DIR__ . '/simple_products_multistore.php';
$storeManager = Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->get(StoreManagerInterface::class);
$product = Bootstrap::getObjectManager()->create(Product::class);
$productRepository = Bootstrap::getObjectManager()->get(ProductRepositoryInterface::class);
$currentStoreId = $storeManager->getStore()->getId();
$secondStoreId = $storeManager->getStore('fixturestore')->getId();
try {
    $product = $productRepository->get('searchspring_simple_1');
    $product->setStoreId($secondStoreId)
        ->setUrlKey('fixturestore-searchspring-simple-1');
    $productRepository->save($product);
    $product = $productRepository->get('searchspring_simple_2');
    $product->setStoreId($secondStoreId)
        ->setUrlKey('fixturestore-searchspring-simple-2');
    $productRepository->save($product);
} finally {
    $storeManager->setCurrentStore($currentStoreId);
}


