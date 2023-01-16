<?php

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Review\Model\Rating;
use Magento\Review\Model\Rating\Option;
use Magento\Review\Model\RatingFactory;
use Magento\Review\Model\Review;
use Magento\Review\Model\ReviewFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

require __DIR__ . '/simple_products.php';

$objectManager = Bootstrap::getObjectManager();
/** @var StoreManagerInterface $storeManager */
$storeManager = $objectManager->get(StoreManagerInterface::class);
$storeId = $storeManager->getStore()->getId();
/** @var ReviewFactory $reviewFactory */
$reviewFactory = $objectManager->get(ReviewFactory::class);
/** @var Review $reviewModel */
$reviewModel = $reviewFactory->create();
/** @var RatingFactory $ratingFactory */
$ratingFactory = $objectManager->get(RatingFactory::class);

$reviewTemplate = [
    'nickname' => 'test nickname',
    'title' => 'test title',
    'detail' => 'test details',
    'customer_id' => null,
    'status_id' => Review::STATUS_APPROVED,
    'stores' => [$storeId],
    'store_id' => $storeId,
    'entity_id' => $reviewModel->getEntityIdByCode(Review::ENTITY_PRODUCT_CODE)
];

$ratingsConfig = [
    'searchspring_simple_1' => [
        ['value' => 1, 'status_id' => Review::STATUS_APPROVED],
        ['value' => 2, 'status_id' => Review::STATUS_APPROVED],
        ['value' => 3, 'status_id' => Review::STATUS_PENDING]
    ],
    'searchspring_simple_2' => [
        ['value' => 3, 'status_id' => Review::STATUS_APPROVED],
        ['value' => 4, 'status_id' => Review::STATUS_APPROVED],
        ['value' => 5, 'status_id' => Review::STATUS_APPROVED],
        ['value' => 5, 'status_id' => Review::STATUS_PENDING],
        ['value' => 2, 'status_id' => Review::STATUS_PENDING]
    ]
];

// activate rating options and build value map
/** @var \Magento\Review\Model\ResourceModel\Review\Collection $ratingCollection */
$ratingCollection = $objectManager->create(Rating::class)->getCollection()
    ->setPageSize(1)
    ->setCurPage(1);

$ratingsMap = [];
foreach ($ratingCollection as $rating) {
    $rating->setStores([$storeId])->setIsActive(1)->save();
    $ratingId = $rating->getId();
    $ratingOptionCollection = $objectManager
        ->create(Option::class)
        ->getCollection()
        ->addRatingFilter($rating->getId());
    foreach ($ratingOptionCollection as $ratingOption) {
        $ratingsMap[$ratingId][$ratingOption->getValue()] = $ratingOption->getId();
    }
}

/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
/** @var SearchCriteriaBuilder $searchCriteriaBuilder */
$searchCriteriaBuilder = $objectManager->get(SearchCriteriaBuilder::class);
$searchCriteria = $searchCriteriaBuilder->addFilter('sku', 'searchspring_simple%', 'like')
    ->create();

foreach ($productRepository->getList($searchCriteria)->getItems() as $product) {
    $sku = $product->getSku();
    $id = $product->getId();
    $skuRatingConfig = $ratingsConfig[$sku] ?? [];
    foreach ($skuRatingConfig as $ratingConfig) {
        $reviewData = $reviewTemplate;
        $reviewData['entity_pk_value'] = $id;
        $reviewData['status_id'] = $ratingConfig['status_id'];
        $review = $reviewFactory->create();
        $review->setData($reviewData);
        $review->save();
        foreach ($ratingsMap as $ratingId => $ratingOptions) {
            $optionId = $ratingOptions[$ratingConfig['value']];
            $ratingFactory->create()
                ->setRatingId($ratingId)
                ->setReviewId($review->getId())
                ->setCustomerId(null)
                ->addOptionVote($optionId, $id);
        }

        $review->aggregate();
    }
}
