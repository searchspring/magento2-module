<?php

use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var ConfigInterface $config */
$config = $objectManager->get(ConfigInterface::class);
$config->saveConfig('catalog/seo/product_url_suffix', null);
$config->saveConfig('catalog/seo/category_url_suffix', null);
$objectManager->get(ReinitableConfigInterface::class)->reinit();
