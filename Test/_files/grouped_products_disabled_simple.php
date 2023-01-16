<?php

use Magento\Catalog\Api\Data\ProductLinkInterface;
use Magento\Catalog\Api\Data\ProductLinkInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Visibility;
use SearchSpring\Feed\Test\Integration\BackwardCompatibility\Grouped\Inventory\ChangeParentStockStatus;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(ProductRepositoryInterface::class);
/** @var ProductLinkInterfaceFactory $productLinkFactory */
$productLinkFactory = Bootstrap::getObjectManager()->get(ProductLinkInterfaceFactory::class);
$productIds = ['1030', '1031', '1032', '1033'];

foreach ($productIds as $productId) {
    /** @var $product Product */
    $product = $objectManager->create(Product::class);
    $product->setTypeId(Type::TYPE_SIMPLE)
        ->setWebsiteIds([1])
        ->setAttributeSetId(4)
        ->setName('SearchSpring Grouped 2 Test Disabled Simple ' . $productId)
        ->setSku('searchspring_grouped_test_disabled_simple_' . $productId)
        ->setPrice(100)
        ->setVisibility(Visibility::VISIBILITY_BOTH)
        ->setStatus(Status::STATUS_DISABLED)
        ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
        ->setData('boolean_attribute', false)
        ->setData('decimal_attribute', $productId);

    $linkedProducts[] = $productRepository->save($product);
}

/** @var $product Product */
$product = $objectManager->create(Product::class);

$product->setTypeId(Grouped::TYPE_CODE)
    ->setId(1)
    ->setWebsiteIds([1])
    ->setAttributeSetId(4)
    ->setName('SearchSpring Grouped Test Grouped Product')
    ->setSku('searchspring_grouped_test_disabled_simple_grouped')
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


/** @var ChangeParentStockStatus $stockProcessor */
$stockProcessor = $objectManager->get(ChangeParentStockStatus::class);
foreach ($linkedProducts as $linkedProduct) {
    $stockProcessor->execute((int) $linkedProduct->getId());
}
