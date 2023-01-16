<?php

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

$storeManager = Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->get(StoreManagerInterface::class);
$product = Bootstrap::getObjectManager()->create(Product::class);
$productRepository = Bootstrap::getObjectManager()->get(ProductRepositoryInterface::class);
$product->setTypeId('simple')
    ->setAttributeSetId(4)
    ->setName('Simple 1')
    ->setSku('searchspring_simple_1')
    ->setPrice(10)
    ->setTaxClassId(0)
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(
        [
            'qty' => 100,
            'is_in_stock' => 1,
            'manage_stock' => 1,
            'use_config_manage_stock' => 0
        ]
    )
    ->setWebsiteIds([$storeManager->getStore()->getWebsiteId()])
    ->setCategoryIds([2])
    ->setData('boolean_attribute', true)
    ->setData('decimal_attribute', 50);
$productRepository->save($product);
$product = Bootstrap::getObjectManager()->create(Product::class);
$product->setTypeId('simple')
    ->setAttributeSetId(4)
    ->setName('Simple 2')
    ->setSku('searchspring_simple_2')
    ->setPrice(10)
    ->setTaxClassId(0)
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(
        [
            'qty' => 100,
            'is_in_stock' => 1,
            'manage_stock' => 1,
        ]
    )
    ->setWebsiteIds([$storeManager->getStore()->getWebsiteId()])
    ->setCategoryIds([2])
    ->setData('boolean_attribute', false)
    ->setData('decimal_attribute', 100);
$productRepository->save($product);

