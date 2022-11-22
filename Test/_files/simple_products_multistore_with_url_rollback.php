<?php
/**
 *  @author Dmitry Kisten <dkisten@absoluteweb.com>
 *  @author Absolute Web Services <info@absoluteweb.com>
 *  @copyright Copyright (c) 2021, Focus Camera, Inc.
 */

use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('SearchSpring_Feed::Test/_files/simple_products_rollback.php');
