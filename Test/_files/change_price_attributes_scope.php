<?php

use Magento\Catalog\Observer\SwitchPriceAttributeScopeOnConfigChange;
use Magento\Eav\Model\Config;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Store\Model\Store;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var ReinitableConfigInterface $reinitiableConfig */
$reinitiableConfig = $objectManager->get(ReinitableConfigInterface::class);
$reinitiableConfig->setValue(
    'catalog/price/scope',
    Store::PRICE_SCOPE_WEBSITE
);
$observer = $objectManager->get(Observer::class);
$objectManager->get(SwitchPriceAttributeScopeOnConfigChange::class)
    ->execute($observer);

$objectManager->get(Config::class)
    ->clear();
