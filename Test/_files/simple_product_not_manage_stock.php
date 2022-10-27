<?php
/**
 *  @author Dmitry Kisten <dkisten@absoluteweb.com>
 *  @author Absolute Web Services <info@absoluteweb.com>
 *  @copyright Copyright (c) 2021, Focus Camera, Inc.
 */
$storeManager = Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->get(\Magento\Store\Model\StoreManagerInterface::class);
$product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\Magento\Catalog\Model\Product::class);
$productRepository = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
$product->setTypeId('simple')
    ->setAttributeSetId(4)
    ->setName('Simple Not Manage Stock')
    ->setSku('searchspring_simple_not_manage_stock')
    ->setPrice(10)
    ->setTaxClassId(0)
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setStockData(
        [
            'use_config_manage_stock' => 0,
            'manage_stock' => 0,
            'use_config_enable_qty_increments' => 1,
            'use_config_qty_increments' => 1,
            'is_in_stock' => 0,
        ]
    )
    ->setWebsiteIds([$storeManager->getStore()->getWebsiteId()])
    ->setData('boolean_attribute', true)
    ->setData('decimal_attribute', 50);
$productRepository->save($product);
