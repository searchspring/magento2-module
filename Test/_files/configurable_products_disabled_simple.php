<?php

use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Visibility;
use Magento\ConfigurableProduct\Helper\Product\Options\Factory;
use Magento\ConfigurableProduct\Model\Inventory\ChangeParentStockStatus;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Eav\Api\Data\AttributeOptionInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;

require __DIR__ . '/configurable_attribute_first.php';

$objectManager = Bootstrap::getObjectManager();

/** @var WebsiteRepositoryInterface $websiteRepository */
$websiteRepository = $objectManager->get(WebsiteRepositoryInterface::class);
$baseWebsite = $websiteRepository->get('base');

/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(ProductRepositoryInterface::class);
/** @var ProductInterfaceFactory $productInterfaceFactory */
$productInterfaceFactory = $objectManager->get(ProductInterfaceFactory::class);

/** @var ProductAttributeRepositoryInterface $attributeRepository */
$attributeRepository = $objectManager->get(ProductAttributeRepositoryInterface::class);
/** @var $firstAttribute Attribute */
$firstAttribute = $attributeRepository->get('test_configurable_first');
/** @var AttributeOptionInterface[] $firstAttributeOptions */
$firstAttributeOptions = $firstAttribute->getOptions();

/** @var $installer EavSetup */
$installer = $objectManager->get(EavSetup::class);
$attributeSetId = $installer->getAttributeSetId(Product::ENTITY, 'Default');

/** @var Factory $optionsFactory */
$optionsFactory = $objectManager->get(Factory::class);
/* Create simple products per each option value*/

$attributeValues = [];
$associatedProductIds = [];
$productIds = [100, 200, 300, 400];
array_shift($firstAttributeOptions); //remove the first option which is empty

foreach ($firstAttributeOptions as $option) {
    /** @var $product Product */
    $product = $productInterfaceFactory->create();
    $productId = array_shift($productIds);
    $product->setTypeId(Type::TYPE_SIMPLE)
        ->setAttributeSetId($attributeSetId)
        ->setWebsiteIds([$baseWebsite->getId()])
        ->setName('SearchSpring Test Configurable Option Disabled Simple' . $option->getLabel())
        ->setSku('searchspring_configurable_test_disabled_simple_' . $productId)
        ->setPrice($productId)
        ->setTestConfigurableFirst($option->getValue())
        ->setVisibility(Visibility::VISIBILITY_NOT_VISIBLE)
        ->setStatus(Status::STATUS_DISABLED)
        ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
        ->setData('boolean_attribute', true)
        ->setData('decimal_attribute', $productId);
    $simple1 = $productRepository->save($product);

    $attributeValues[] = [
        'label' => 'test',
        'attribute_id' => $firstAttribute->getId(),
        'value_index' => $option->getValue(),
    ];
    $associatedProductIds[] = $simple1->getId();
}

/** @var $product Product */
$product = $productInterfaceFactory->create();
$configurableAttributesData = [
    [
        'attribute_id' => $firstAttribute->getId(),
        'code' => $firstAttribute->getAttributeCode(),
        'label' => $firstAttribute->getStoreLabel(),
        'position' => '0',
        'values' => $attributeValues,
    ],
];
$configurableOptions = $optionsFactory->create($configurableAttributesData);
$extensionConfigurableAttributes = $product->getExtensionAttributes();
$extensionConfigurableAttributes->setConfigurableProductOptions($configurableOptions);
$extensionConfigurableAttributes->setConfigurableProductLinks($associatedProductIds);
$product->setExtensionAttributes($extensionConfigurableAttributes);

$product->setTypeId(Configurable::TYPE_CODE)
    ->setAttributeSetId($attributeSetId)
    ->setWebsiteIds([$baseWebsite->getId()])
    ->setName('SearchSpring Configurable Product Test Disabled Simple')
    ->setSku('searchspring_configurable_test_disabled_simple_configurable')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 1, 'is_in_stock' => 1]);
$productRepository->cleanCache();
$productRepository->save($product);

/** @var ChangeParentStockStatus $stockProcessor */
$stockProcessor = $objectManager->get(ChangeParentStockStatus::class);
$stockProcessor->execute($associatedProductIds);
