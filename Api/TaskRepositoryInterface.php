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

namespace SearchSpring\Feed\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use SearchSpring\Feed\Api\Data\TaskInterface;
use SearchSpring\Feed\Api\Data\TaskSearchResultsInterface;

interface TaskRepositoryInterface
{
    /**
     * @param int $id
     * @return TaskInterface
     * @throws NoSuchEntityException
     * @return TaskInterface
     */
    public function get(int $id) : TaskInterface;

    /**
     * @param SearchCriteriaInterface|null $searchCriteria
     * @return TaskSearchResultsInterface
     * @throws LocalizedException
     * @return TaskSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null) : TaskSearchResultsInterface;

    /**
     * @param TaskInterface $task
     * @return TaskInterface
     * @throws CouldNotSaveException
     * @return TaskInterface
     */
    public function save(TaskInterface $task) : TaskInterface;

    /**
     * @param TaskInterface $task
     * @throws CouldNotDeleteException
     * @return void
     */
    public function delete(TaskInterface $task) : void;

    /**
     * @param int $id
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     * @return void
     */
    public function deleteById(int $id) : void;
}
