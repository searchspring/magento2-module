<?php
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Observer\SwitchPriceAttributeScopeOnConfigChange;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Store/_files/second_website_with_store_group_and_store_rollback.php');
Resolver::getInstance()->requireDataFixture('SearchSpring_Feed::Test/_files/simple_products_rollback.php');
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
