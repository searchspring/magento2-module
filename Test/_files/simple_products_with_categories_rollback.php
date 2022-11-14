<?php

use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('SearchSpring_Feed::Test/_files/simple_products_rollback.php');
Resolver::getInstance()->requireDataFixture('SearchSpring_Feed::Test/_files/categories_rollback.php');
