<?php

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('SearchSpring_Feed::Test/_files/configurable_products.php');
/** @var ObjectManager $objectManager */
$objectManager = Bootstrap::getObjectManager();
$productRepository = $objectManager->get(ProductRepositoryInterface::class);
/** @var Product $product10 */
$product10 = $productRepository->get('searchspring_configurable_test_simple_10');
/** @var Product $product20 */
$product20 = $productRepository->get('searchspring_configurable_test_simple_20');
/** @var Product $product30 */
$product30 = $productRepository->get('searchspring_configurable_test_simple_30');
/** @var Product $product40 */
$product40 = $productRepository->get('searchspring_configurable_test_simple_40');
$product10->setSpecialPrice(6);
$product10->setSpecialFromDate(date('Y-m-d', strtotime('-3 day')));
$product10->setSpecialToDate(date('Y-m-d', strtotime('+5 day')));
$product20->setSpecialPrice(2);
$product20->setSpecialFromDate(date('Y-m-d', strtotime('-4 day')));
$product20->setSpecialToDate(date('Y-m-d', strtotime('-2 day')));
$product30->setSpecialPrice(20);
$product30->setSpecialFromDate(date('Y-m-d', strtotime('+3 day')));
$product30->setSpecialToDate(date('Y-m-d', strtotime('+5 day')));
$product40->setSpecialPrice(25);
$product40->setSpecialFromDate(date('Y-m-d', strtotime('-3 day')));
$product40->setSpecialToDate(date('Y-m-d', strtotime('+5 day')));
$productRepository->save($product10);
$productRepository->save($product20);
$productRepository->save($product30);
$productRepository->save($product40);

