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

/** @var Store $store */

use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

$store = Bootstrap::getObjectManager()->create(Store::class);
if (!$store->load('inactive_store', 'code')->getId()) {
    $websiteId = Bootstrap::getObjectManager()->get(
        StoreManagerInterface::class
    )->getWebsite()
        ->getId();
    $groupId = Bootstrap::getObjectManager()->get(
        StoreManagerInterface::class
    )->getWebsite()->getDefaultGroupId();
    $store->setCode(
        'inactive_store'
    )->setWebsiteId(
        $websiteId
    )->setGroupId(
        $groupId
    )->setName(
        'Inactive Store'
    )->setSortOrder(
        15
    )->setIsActive(
        0
    );
    $store->save();
}
