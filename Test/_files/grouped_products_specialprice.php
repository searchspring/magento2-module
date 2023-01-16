<?php

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;

require __DIR__ . '/grouped_products.php';
/** @var ObjectManager $objectManager */
$objectManager = Bootstrap::getObjectManager();
$productRepository = $objectManager->get(ProductRepositoryInterface::class);
/** @var Product $product1010 */
$product1010 = $productRepository->get('searchspring_grouped_test_simple_1010');
/** @var Product $product1011 */
$product1011 = $productRepository->get('searchspring_grouped_test_simple_1011');
/** @var Product $product1012 */
$product1012 = $productRepository->get('searchspring_grouped_test_simple_1012');
/** @var Product $product1013 */
$product1013 = $productRepository->get('searchspring_grouped_test_simple_1013');
$product1010->setSpecialPrice(2800);
$product1010->setSpecialFromDate(date('Y-m-d', strtotime('-3 day')));
$product1010->setSpecialToDate(date('Y-m-d', strtotime('+5 day')));
$product1011->setSpecialPrice(900);
$product1011->setSpecialFromDate(date('Y-m-d', strtotime('-4 day')));
$product1011->setSpecialToDate(date('Y-m-d', strtotime('-2 day')));
$product1012->setSpecialPrice(800);
$product1012->setSpecialFromDate(date('Y-m-d', strtotime('+3 day')));
$product1012->setSpecialToDate(date('Y-m-d', strtotime('+5 day')));
$product1013->setSpecialPrice(1000);
$product1013->setSpecialFromDate(date('Y-m-d', strtotime('-3 day')));
$product1013->setSpecialToDate(date('Y-m-d', strtotime('+5 day')));
$productRepository->save($product1010);
$productRepository->save($product1011);
$productRepository->save($product1012);
$productRepository->save($product1013);
