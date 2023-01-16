<?php

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

require __DIR__ . '/../../../../../../dev/tests/integration/testsuite/Magento/Store/_files/core_fixturestore.php';
require __DIR__ . '/simple_products.php';
$storeManager = Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->get(StoreManagerInterface::class);
$product = Bootstrap::getObjectManager()->create(Product::class);
$productRepository = Bootstrap::getObjectManager()->get(ProductRepositoryInterface::class);
/** @var Magento\Store\Model\Store $store */
$store = Bootstrap::getObjectManager()->create(Store::class);
$store->load('fixturestore', 'code');
$product = $productRepository->get('searchspring_simple_1');
$product->setStoreId($store->getId())
    ->setName('StoreTitle');
$productRepository->save($product);
$product = $productRepository->get('searchspring_simple_2');
$product->setStoreId($store->getId())
    ->setName('StoreTitle');
$productRepository->save($product);

