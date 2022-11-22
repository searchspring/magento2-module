<?php

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('SearchSpring_Feed::Test/_files/simple_products.php');
$product = Bootstrap::getObjectManager()->create(Product::class);
$productRepository = Bootstrap::getObjectManager()->get(ProductRepositoryInterface::class);
/** @var Product $product */
$product = $productRepository->get('searchspring_simple_1');
$product->setUrlKey('searchspring-simple-1');
$productRepository->save($product);
$product = $productRepository->get('searchspring_simple_2');
$product->setUrlKey('searchspring-simple-2');
$productRepository->save($product);
