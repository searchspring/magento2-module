<?php

use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('SearchSpring_Feed::Test/_files/simple_products.php');
Resolver::getInstance()->requireDataFixture('SearchSpring_Feed::Test/_files/categories.php');

$objectManager = Bootstrap::getObjectManager();
/** @var CategoryLinkManagementInterface $categoryLinkManagement */
$categoryLinkManagement = $objectManager->get(CategoryLinkManagementInterface::class);
$categoryLinkManagement->assignProductToCategories(
    'searchspring_simple_1',
    [1000, 1001, 1002, 1011, 1012]
);

$categoryLinkManagement->assignProductToCategories(
    'searchspring_simple_2',
    [1002, 1012, 1020, 1021]
);
