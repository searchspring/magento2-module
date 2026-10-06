<?php
/**
 * Copyright (C) 2026 Searchspring <https://searchspring.com>
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

namespace SearchSpring\Feed\Api;

use SearchSpring\Feed\Api\Data\ProductInfoResponseInterface;

interface ProductInfoInterface
{
    /**
     * Run the feed collection and data providers for one product, using the latest feed task payload of the store.
     *
     * @param int $productId
     * @param int $storeId
     * @return \SearchSpring\Feed\Api\Data\ProductInfoResponseInterface
     */
    public function getInfo(int $productId, int $storeId = 1): ProductInfoResponseInterface;
}
