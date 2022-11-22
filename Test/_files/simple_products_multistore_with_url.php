<?php

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('SearchSpring_Feed::Test/_files/simple_products_multistore.php');
$storeManager = Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->get(StoreManagerInterface::class);
$product = Bootstrap::getObjectManager()->create(Product::class);
$productRepository = Bootstrap::getObjectManager()->get(ProductRepositoryInterface::class);
/** @var Magento\Store\Model\Store $store */
$store = Bootstrap::getObjectManager()->create(Store::class);
$store->load('fixturestore', 'code');
$product = $productRepository->get('searchspring_simple_1');
$product->setStoreId($store->getId())
    ->setUrlKey('fixturestore-searchspring-simple-1');
$productRepository->save($product);
$product = $productRepository->get('searchspring_simple_2');
$product->setStoreId($store->getId())
    ->setUrlKey('fixturestore-searchspring-simple-2');
$productRepository->save($product);

