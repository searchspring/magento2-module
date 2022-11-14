<?php

use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

$objectManager = Bootstrap::getObjectManager();
Resolver::getInstance()->requireDataFixture('SearchSpring_Feed::Test/_files/simple_products_with_categories.php');
Resolver::getInstance()->requireDataFixture('SearchSpring_Feed::Test/_files/categories_second_website.php');

/** @var WebsiteRepositoryInterface $websiteRepository */
$websiteRepository = $objectManager->get(WebsiteRepositoryInterface::class);
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(ProductRepositoryInterface::class);
$website = $websiteRepository->get('test');
$websiteId = (int) $website->getId();

$skus = [
    'searchspring_simple_1' => [1000, 1001, 1002, 1011, 1012, 2000, 2001],
    'searchspring_simple_2' => [1002, 1012, 1020, 1021, 2000, 2001]
];
/** @var CategoryLinkManagementInterface $categoryLinkManagement */
$categoryLinkManagement = $objectManager->get(CategoryLinkManagementInterface::class);

foreach ($skus as $sku => $categoryIds) {
    $product = $productRepository->get($sku, true);
    $product->setWebsiteIds(array_merge($product->getWebsiteIds(), [$websiteId]));
    $productRepository->save($product);
    $categoryLinkManagement->assignProductToCategories($sku, $categoryIds);
}
