<?php
/**
 * Copyright (C) 2023 Searchspring <https://searchspring.com>
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\Group;
use Magento\Store\Model\GroupFactory;
use Magento\Store\Model\ResourceModel\Group as GroupResource;
use Magento\Store\Model\ResourceModel\Store as StoreResource;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

require __DIR__ . '/website.php';

$objectManager = Bootstrap::getObjectManager();
$categoryCollectionFactory = $objectManager->get(CollectionFactory::class);
/** @var WebsiteRepositoryInterface $websiteRepository */
$websiteRepository = $objectManager->get(WebsiteRepositoryInterface::class);
$website = $websiteRepository->get('test');

/** @var Collection $categoryCollection */
$categoryCollection = $categoryCollectionFactory->create();
$rootCategory = $categoryCollection
    ->addAttributeToFilter(CategoryInterface::KEY_NAME, 'Second Root Category')
    ->setPageSize(1)
    ->getFirstItem();

$categoryFactory = $objectManager->get(CategoryFactory::class);
$categoryRepository = $objectManager->create(CategoryRepositoryInterface::class);

/** @var Category $rootCategory */
$rootCategory = $categoryFactory->create();
$rootCategory->isObjectNew(true);
$rootCategory->setName('Second Root Category')
    ->setParentId(Category::TREE_ROOT_ID)
    ->setIsActive(true)
    ->setPosition(2);
$rootCategory = $categoryRepository->save($rootCategory);

$groupFactory = $objectManager->get(GroupFactory::class);
/** @var GroupResource $groupResource */
$groupResource = $objectManager->create(GroupResource::class);
/** @var Group $storeGroup */
$storeGroup = $groupFactory->create();
$storeGroup->setCode('test_store_group_1')
    ->setName('Test Store Group 1')
    ->setRootCategoryId($rootCategory->getId())
    ->setWebsite($website);
$groupResource->save($storeGroup);

$storeFactory = $objectManager->get(StoreFactory::class);
/** @var StoreResource $storeResource */
$storeResource = $objectManager->create(StoreResource::class);
/** @var Store $store */
$store = $storeFactory->create();
$store->setCode('test_store_1')
    ->setName('Test Store 1')
    ->setWebsiteId($website->getId())
    ->setGroup($storeGroup)
    ->setSortOrder(10)
    ->setIsActive(1);
$storeResource->save($store);

/* Refresh stores memory cache */
$objectManager->get(StoreManagerInterface::class)->reinitStores();
