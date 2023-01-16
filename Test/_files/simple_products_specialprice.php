<?php

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;

require __DIR__ . '/simple_products.php';
/** @var ObjectManager $objectManager */
$objectManager = Bootstrap::getObjectManager();
$productRepository = $objectManager->get(ProductRepositoryInterface::class);
/** @var Product $product1 */
$product1 = $productRepository->get('searchspring_simple_1');
/** @var Product $product2 */
$product2 = $productRepository->get('searchspring_simple_2');
$product1->setSpecialPrice(5);
$product1->setSpecialFromDate(date('Y-m-d', strtotime('+3 day')));
$product1->setSpecialToDate(date('Y-m-d', strtotime('+5 day')));
$product2->setSpecialPrice(6);
$product2->setSpecialFromDate(date('Y-m-d', strtotime('-3 day')));
$product2->setSpecialToDate(date('Y-m-d', strtotime('+5 day')));
$productRepository->save($product1);
$productRepository->save($product2);
