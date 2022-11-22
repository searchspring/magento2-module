<?php

use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var ConfigInterface $config */
$config = $objectManager->get(ConfigInterface::class);
$config->deleteConfig('catalog/seo/product_url_suffix');
$config->deleteConfig('catalog/seo/category_url_suffix');
$objectManager->get(ReinitableConfigInterface::class)->reinit();
