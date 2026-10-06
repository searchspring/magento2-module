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

namespace SearchSpring\Feed\Model\Data;

use Magento\Framework\DataObject;
use SearchSpring\Feed\Api\Data\ProductInfoResponseInterface;

class ProductInfoResponse extends DataObject implements ProductInfoResponseInterface
{
    private const PRODUCT_IDS = 'product_ids';
    private const PRODUCT_INFO = 'product_info';
    private const STORE_CODE = 'store_code';
    private const TASK_ID = 'task_id';
    private const QUERY = 'query';
    private const MESSAGE = 'message';

    /**
     * @return int[]
     */
    public function getProductIds(): array
    {
        return (array) $this->getData(self::PRODUCT_IDS);
    }

    /**
     * @param int[] $productIds
     * @return $this
     */
    public function setProductIds(array $productIds)
    {
        return $this->setData(self::PRODUCT_IDS, $productIds);
    }

    /**
     * @return mixed[]
     */
    public function getProductInfo(): array
    {
        return (array) $this->getData(self::PRODUCT_INFO);
    }

    /**
     * @param mixed[] $productInfo
     * @return $this
     */
    public function setProductInfo(array $productInfo)
    {
        return $this->setData(self::PRODUCT_INFO, $productInfo);
    }

    /**
     * @return string|null
     */
    public function getStoreCode(): ?string
    {
        return $this->getData(self::STORE_CODE);
    }

    /**
     * @param string|null $storeCode
     * @return $this
     */
    public function setStoreCode(?string $storeCode)
    {
        return $this->setData(self::STORE_CODE, $storeCode);
    }

    /**
     * @return int|null
     */
    public function getTaskId(): ?int
    {
        $taskId = $this->getData(self::TASK_ID);
        return $taskId !== null ? (int) $taskId : null;
    }

    /**
     * @param int|null $taskId
     * @return $this
     */
    public function setTaskId(?int $taskId)
    {
        return $this->setData(self::TASK_ID, $taskId);
    }

    /**
     * @return string|null
     */
    public function getQuery(): ?string
    {
        return $this->getData(self::QUERY);
    }

    /**
     * @param string|null $query
     * @return $this
     */
    public function setQuery(?string $query)
    {
        return $this->setData(self::QUERY, $query);
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * @param string|null $message
     * @return $this
     */
    public function setMessage(?string $message)
    {
        return $this->setData(self::MESSAGE, $message);
    }
}
