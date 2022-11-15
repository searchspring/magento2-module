<?php

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('SearchSpring_Feed::Test/_files/simple_products.php');

$objectManager = Bootstrap::getObjectManager();
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
/** @var SearchCriteriaBuilder $searchCriteriaBuilder */
$searchCriteriaBuilder = $objectManager->get(SearchCriteriaBuilder::class);
$searchCriteria = $searchCriteriaBuilder->addFilter('sku', 'searchspring_simple%', 'like')
    ->create();
foreach ($productRepository->getList($searchCriteria)->getItems() as $product) {
    Resolver::getInstance()->requireDataFixture('SearchSpring_Feed::Test/_files/product_image.php');
    $sku = $product->getSku();
    /** @var $product Product */
    $product->setStoreId(0)
        ->setImage('/m/a/magento_image.jpg')
        ->setSmallImage('/m/a/magento_small_image.jpg')
        ->setThumbnail('/m/a/magento_thumbnail.jpg')
        ->setData('media_gallery', ['images' => [
            [
                'file' => '/m/a/magento_image.jpg',
                'position' => 1,
                'label' => 'Image Alt Text',
                'disabled' => 0,
                'media_type' => 'image'
            ],
            [
                'file' => '/m/a/magento_small_image.jpg',
                'position' => 2,
                'label' => 'Small Image Alt Text',
                'disabled' => 0,
                'media_type' => 'image'
            ],
            [
                'file' => '/m/a/magento_thumbnail.jpg',
                'position' => 3,
                'label' => 'Thumbnail Image Alt Text',
                'disabled' => 0,
                'media_type' => 'image'
            ],
        ]])
        ->setCanSaveCustomOptions(true);
    $product->save();
}
