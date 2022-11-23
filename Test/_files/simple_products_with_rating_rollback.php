<?php

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Registry;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory;
use Magento\Review\Model\Review;
use Magento\Review\Model\ReviewFactory;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);
$currentArea = $registry->registry('isSecureArea');
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);
/** @var CollectionFactory $collectionFactory */
$collectionFactory = $objectManager->get(CollectionFactory::class);
/** @var ReviewFactory $reviewFactory */
$reviewFactory = $objectManager->get(ReviewFactory::class);
/** @var Review $reviewModel */
$reviewModel = $reviewFactory->create();
$entityId = $reviewModel->getEntityIdByCode(Review::ENTITY_PRODUCT_CODE);
/** @var ProductRepositoryInterface $productRepository */
$productRepository = Bootstrap::getObjectManager()
    ->get(ProductRepositoryInterface::class);
/** @var SearchCriteriaBuilder $searchCriteriaBuilder */
$searchCriteriaBuilder = $objectManager->get(SearchCriteriaBuilder::class);
$searchCriteria = $searchCriteriaBuilder->addFilter('sku', 'searchspring_simple_%', 'like')
    ->create();
foreach ($productRepository->getList($searchCriteria)->getItems() as $item) {
    $collection = $collectionFactory->create();
    $collection->addEntityFilter($entityId, $item->getId());
    foreach ($collection as $review) {
        $review->delete();
    }
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', $currentArea);

Resolver::getInstance()->requireDataFixture('SearchSpring_Feed::Test/_files/simple_products_rollback.php');
