<?php

/** @var Registry $registry */

use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$registry = Bootstrap::getObjectManager()->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);
$productCollection = Bootstrap::getObjectManager()
    ->get(\Magento\Catalog\Model\ResourceModel\Product\Collection::class);
foreach ($productCollection as $product) {
    $product->delete();
}

$eavConfig = Bootstrap::getObjectManager()->get(Config::class);
$attribute = $eavConfig->getAttribute('catalog_product', 'test_configurable_first');
if ($attribute instanceof AbstractAttribute
    && $attribute->getId()
) {
    $attribute->delete();
}
$eavConfig->clear();

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
