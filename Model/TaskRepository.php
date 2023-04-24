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

namespace SearchSpring\Feed\Model;

use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use SearchSpring\Feed\Api\Data\TaskInterface;
use SearchSpring\Feed\Api\Data\TaskSearchResultsInterface;
use SearchSpring\Feed\Api\Data\TaskSearchResultsInterfaceFactory;
use SearchSpring\Feed\Api\TaskRepositoryInterface;
use SearchSpring\Feed\Model\ResourceModel\Task as TaskResource;
use SearchSpring\Feed\Model\ResourceModel\Task\Collection;
use SearchSpring\Feed\Model\ResourceModel\Task\CollectionFactory;

class TaskRepository implements TaskRepositoryInterface
{
    /**
     * @var TaskFactory
     */
    private $taskFactory;
    /**
     * @var TaskResource
     */
    private $taskResource;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var TaskSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;
    /**
     * @var JoinProcessorInterface
     */
    private $joinProcessor;

    /**
     * TaskRepository constructor.
     * @param TaskFactory $taskFactory
     * @param TaskResource $taskResource
     * @param CollectionFactory $collectionFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param TaskSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $joinProcessor
     */
    public function __construct(
        TaskFactory $taskFactory,
        TaskResource $taskResource,
        CollectionFactory $collectionFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TaskSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $joinProcessor
    ) {
        $this->taskFactory = $taskFactory;
        $this->taskResource = $taskResource;
        $this->collectionFactory = $collectionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->joinProcessor = $joinProcessor;
    }

    /**
     * @param int $id
     * @return TaskInterface
     * @throws NoSuchEntityException
     * @return TaskInterface
     */
    public function get(int $id): TaskInterface
    {
        /** @var Task $task */
        $task = $this->taskFactory->create();
        $this->taskResource->load($task, $id);
        if (!$task->getEntityId()) {
            throw new NoSuchEntityException(__('The Task with the "%1" ID doesn\'t exist.', $id));
        }

        return $task;
    }

    /**
     * @param SearchCriteriaInterface|null $searchCriteria
     * @return TaskSearchResultsInterface
     * @throws LocalizedException
     * @return TaskSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): TaskSearchResultsInterface
    {
        if (!$searchCriteria) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        }

        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->joinProcessor->process(
            $collection,
            TaskInterface::class
        );
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var TaskSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @param TaskInterface $task
     * @return TaskInterface
     * @throws CouldNotSaveException
     * @return TaskInterface
     */
    public function save(TaskInterface $task): TaskInterface
    {
        try {
            $this->taskResource->save($task);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()), $exception);
        }

        return $task;
    }

    /**
     * @param TaskInterface $task
     * @throws CouldNotDeleteException
     * @return void
     */
    public function delete(TaskInterface $task): void
    {
        try {
            $this->taskResource->delete($task);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()), $exception);
        }
    }

    /**
     * @param int $id
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     * @return void
     */
    public function deleteById(int $id): void
    {
        $this->delete($this->get($id));
    }
}
