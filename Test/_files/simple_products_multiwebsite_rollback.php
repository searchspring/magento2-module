<?php

use Magento\Catalog\Observer\SwitchPriceAttributeScopeOnConfigChange;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Store\Model\Store;
use Magento\TestFramework\Helper\Bootstrap;

require __DIR__ . '/../../../../../../dev/tests/integration/testsuite/Magento/Store/_files/second_website_with_store_group_and_store_rollback.php';
require __DIR__ . '/simple_products_rollback.php';
/** @var ReinitableConfigInterface $reinitiableConfig */
$reinitiableConfig = Bootstrap::getObjectManager()->get(
    ReinitableConfigInterface::class
);
$reinitiableConfig->setValue(
    'catalog/price/scope',
    Store::PRICE_SCOPE_GLOBAL
);
$observer = Bootstrap::getObjectManager()->get(
    Observer::class
);
Bootstrap::getObjectManager()->get(SwitchPriceAttributeScopeOnConfigChange::class)
    ->execute($observer);
