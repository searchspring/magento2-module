<?php

use Magento\Catalog\Api\Data\ProductLinkInterface;
use Magento\Catalog\Api\Data\ProductLinkInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Visibility;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(ProductRepositoryInterface::class);
/** @var ProductLinkInterfaceFactory $productLinkFactory */
$productLinkFactory = Bootstrap::getObjectManager()->get(ProductLinkInterfaceFactory::class);
$productIds = ['1000', '1001'];

foreach ($productIds as $productId) {
    /** @var $product Product */
    $product = $objectManager->create(Product::class);
    $product->setTypeId(Type::TYPE_SIMPLE)
        ->setWebsiteIds([1])
        ->setAttributeSetId(4)
        ->setName('SearchSpring Grouped Test Simple ' . $productId)
        ->setSku('searchspring_grouped_test_simple_' . $productId)
        ->setPrice((int) $productId)
        ->setVisibility(Visibility::VISIBILITY_BOTH)
        ->setStatus(Status::STATUS_ENABLED)
        ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
        ->setData('boolean_attribute', true)
        ->setData('decimal_attribute', $productId);

    $linkedProducts[] = $productRepository->save($product);
}

/** @var $product Product */
$product = $objectManager->create(Product::class);

$product->setTypeId(Grouped::TYPE_CODE)
    ->setWebsiteIds([1])
    ->setAttributeSetId(4)
    ->setName('SearchSpring Grouped Test Grouped Product')
    ->setSku('searchspring_grouped_test_grouped_1')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 1, 'is_in_stock' => 1]);

foreach ($linkedProducts as $linkedProduct) {
    /** @var ProductLinkInterface $productLink */
    $productLink = $productLinkFactory->create();
    $productLink->setSku($product->getSku())
        ->setLinkType('associated')
        ->setLinkedProductSku($linkedProduct->getSku())
        ->setLinkedProductType($linkedProduct->getTypeId())
        ->getExtensionAttributes()
        ->setQty(1);
    $newLinks[] = $productLink;
}

$product->setProductLinks($newLinks);

$productRepository->save($product);
$newLinks = [];
$linkedProducts = [];
$productIds = ['1010', '1011', '1012', '1013'];

foreach ($productIds as $productId) {
    /** @var $product Product */
    $product = $objectManager->create(Product::class);
    $product->setTypeId(Type::TYPE_SIMPLE)
        ->setWebsiteIds([1])
        ->setAttributeSetId(4)
        ->setName('SearchSpring Grouped 2 Test Simple ' . $productId)
        ->setSku('searchspring_grouped_test_simple_' . $productId)
        ->setPrice((int) $productId)
        ->setVisibility(Visibility::VISIBILITY_BOTH)
        ->setStatus(Status::STATUS_ENABLED)
        ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
        ->setData('boolean_attribute', false)
        ->setData('decimal_attribute', $productId);

    $linkedProducts[] = $productRepository->save($product);
}

/** @var $product Product */
$product = $objectManager->create(Product::class);

$product->setTypeId(Grouped::TYPE_CODE)
    ->setWebsiteIds([1])
    ->setAttributeSetId(4)
    ->setName('SearchSpring Grouped Test Grouped Product 2')
    ->setSku('searchspring_grouped_test_grouped_2')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 1, 'is_in_stock' => 1]);

foreach ($linkedProducts as $linkedProduct) {
    /** @var ProductLinkInterface $productLink */
    $productLink = $productLinkFactory->create();
    $productLink->setSku($product->getSku())
        ->setLinkType('associated')
        ->setLinkedProductSku($linkedProduct->getSku())
        ->setLinkedProductType($linkedProduct->getTypeId())
        ->getExtensionAttributes()
        ->setQty(1);
    $newLinks[] = $productLink;
}

$product->setProductLinks($newLinks);

$productRepository->save($product);
