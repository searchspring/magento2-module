<?php

use Magento\Catalog\Api\CategoryListInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var CategoryRepositoryInterface $categoryRepository */
$categoryRepository = $objectManager->get(CategoryRepositoryInterface::class);
/** @var StoreManagerInterface $storeManager */
$storeManager = $objectManager->get(StoreManagerInterface::class);
/** @var Store $store */
$store = $storeManager->getStore('default');
$rootCategory = $store->getRootCategoryId();
/** @var SearchCriteriaBuilder $searchCriteriaBuilder */
$searchCriteriaBuilder = $objectManager->get(SearchCriteriaBuilder::class);
$searchCriteria = $searchCriteriaBuilder->addFilter('path', "%/$rootCategory/%", 'like')
    ->create();

/** @var CategoryListInterface $categoryList */
$categoryList = $objectManager->get(CategoryListInterface::class);

/** @var Category $item */
foreach ($categoryList->getList($searchCriteria)->getItems() as $item) {
    $name = 'Store default ' . $item->getName();
    $item->setName($name)
        ->setStoreId((int) $store->getId());

    $categoryRepository->save($item);
}
