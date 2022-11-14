<?php

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Model\Stock\Status;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

$objectManager = Bootstrap::getObjectManager();

/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var ProductRepositoryInterface $productRepository */
$productRepository = Bootstrap::getObjectManager()
    ->get(ProductRepositoryInterface::class);
/** @var SearchCriteriaBuilder $searchCriteriaBuilder */
$searchCriteriaBuilder = $objectManager->get(SearchCriteriaBuilder::class);
$searchCriteria = $searchCriteriaBuilder->addFilter('sku', 'searchspring_configurable_test_disabled_simple%', 'like')
    ->create();


foreach ($productRepository->getList($searchCriteria)->getItems() as $product) {
    try {
        $stockStatus = $objectManager->create(Status::class);
        $stockStatus->load($product->getEntityId(), 'product_id');
        $stockStatus->delete();

        $productRepository->delete($product);
    } catch (NoSuchEntityException $e) {
        //Product already removed
    }
}

Resolver::getInstance()->requireDataFixture('Magento/ConfigurableProduct/_files/configurable_attribute_first_rollback.php');

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
