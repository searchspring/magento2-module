<?php

use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\TestFramework\Helper\Bootstrap;

require __DIR__ . '/simple_products.php';
require __DIR__ . '/categories.php';

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
