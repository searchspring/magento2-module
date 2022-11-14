<?php

use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('SearchSpring_Feed::Test/_files/simple_products_with_categories_rollback.php');
Resolver::getInstance()->requireDataFixture('SearchSpring_Feed::Test/_files/categories_second_website_rollback.php');
