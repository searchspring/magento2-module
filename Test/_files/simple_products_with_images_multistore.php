<?php

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Store\Model\Store;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Store/_files/core_fixturestore.php');
Resolver::getInstance()->requireDataFixture('SearchSpring_Feed::Test/_files/simple_products_with_images.php');

$objectManager = Bootstrap::getObjectManager();
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
/** @var Store $store */
$store = $objectManager->create(Store::class);
$store->load('fixturestore', 'code');
/** @var SearchCriteriaBuilder $searchCriteriaBuilder */
$searchCriteriaBuilder = $objectManager->get(SearchCriteriaBuilder::class);
$searchCriteria = $searchCriteriaBuilder->addFilter('sku', 'searchspring_simple%', 'like')
    ->create();
foreach ($productRepository->getList($searchCriteria)->getItems() as $product) {
    $product->setStoreId($store->getId());
    $mediaGallery = $product->getMediaGallery();
    foreach ($mediaGallery['images'] as &$imageData) {
        $imageData['label'] = 'Store fixturestore ' . $imageData['label'];
    }

    $product->setMediaGallery($mediaGallery);
    $product->save();
}
