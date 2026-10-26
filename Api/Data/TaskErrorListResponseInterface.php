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

interface TaskErrorListResponseInterface
{
    /**
     * @return \SearchSpring\Feed\Api\Data\TaskErrorItemInterface[]
     */
    public function getItems(): array;

    /**
     * @param \SearchSpring\Feed\Api\Data\TaskErrorItemInterface[] $items
     * @return $this
     */
    public function setItems(array $items);

    /**
     * @return int
     */
    public function getTotal(): int;

    /**
     * @param int $total
     * @return $this
     */
    public function setTotal(int $total);

    /**
     * @return int
     */
    public function getCurrentPage(): int;

    /**
     * @param int $currentPage
     * @return $this
     */
    public function setCurrentPage(int $currentPage);

    /**
     * @return int
     */
    public function getPageSize(): int;

    /**
     * @param int $pageSize
     * @return $this
     */
    public function setPageSize(int $pageSize);
}
