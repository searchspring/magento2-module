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

namespace SearchSpring\Feed\Api\Data;

interface ProductInfoResponseInterface
{
    /**
     * @return int[]
     */
    public function getProductIds(): array;

    /**
     * @param int[] $productIds
     * @return $this
     */
    public function setProductIds(array $productIds);

    /**
     * Feed rows as they would be sent to Searchspring.
     *
     * @return mixed[]
     */
    public function getProductInfo(): array;

    /**
     * @param mixed[] $productInfo
     * @return $this
     */
    public function setProductInfo(array $productInfo);

    /**
     * @return string|null
     */
    public function getStoreCode(): ?string;

    /**
     * @param string|null $storeCode
     * @return $this
     */
    public function setStoreCode(?string $storeCode);

    /**
     * Task whose payload was used to build the feed specification.
     *
     * @return int|null
     */
    public function getTaskId(): ?int;

    /**
     * @param int|null $taskId
     * @return $this
     */
    public function setTaskId(?int $taskId);

    /**
     * Product collection SQL after all collection modifiers.
     *
     * @return string|null
     */
    public function getQuery(): ?string;

    /**
     * @param string|null $query
     * @return $this
     */
    public function setQuery(?string $query);

    /**
     * @return string|null
     */
    public function getMessage(): ?string;

    /**
     * @param string|null $message
     * @return $this
     */
    public function setMessage(?string $message);
}
