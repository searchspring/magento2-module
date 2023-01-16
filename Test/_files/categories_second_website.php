<?php

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\TestFramework\Helper\Bootstrap;

require __DIR__ . '/store_with_second_root_category.php';
$objectManager = Bootstrap::getObjectManager();
/** @var CategoryFactory $categoryFactory */
$categoryFactory = $objectManager->get(CategoryFactory::class);
$categoryCollectionFactory = $objectManager->get(CollectionFactory::class);
/** @var Collection $categoryCollection */
$categoryCollection = $categoryCollectionFactory->create();
$rootCategory = $categoryCollection
    ->addAttributeToFilter(CategoryInterface::KEY_NAME, 'Second Root Category')
    ->setPageSize(1)
    ->getFirstItem();

$rootCategoryId = $rootCategory->getId();
$categories = [
    [
        'id' => 2000,
        'name' => 'Category 2000',
        'parent_id' => $rootCategoryId,
        'path' => "1/$rootCategoryId/2000",
        'level' => 2,
        'available_sort_by' => 'name',
        'default_sort_by' => 'name',
        'is_active' => true,
        'position' => 1,
    ],
    [
        'id' => 2001,
        'name' => 'Category 2000.2001',
        'parent_id' => 2000,
        'path' => "1/$rootCategoryId/2000/2001",
        'level' => 3,
        'available_sort_by' => 'name',
        'default_sort_by' => 'name',
        'is_active' => true,
        'position' => 1
    ]
];

foreach ($categories as $data) {
    /** @var \Magento\Catalog\Model\Category $model */
    $model = $categoryFactory->create();
    $model->isObjectNew(true);
    $model->setId($data['id'])
        ->setName($data['name'])
        ->setParentId($data['parent_id'])
        ->setPath($data['path'])
        ->setLevel($data['level'])
        ->setAvailableSortBy($data['available_sort_by'])
        ->setDefaultSortBy($data['default_sort_by'])
        ->setIsActive($data['is_active'])
        ->setPosition($data['position'])
        ->setStoreId(0)
        ->save();
}
