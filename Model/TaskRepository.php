<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model;

use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
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
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;
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
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param TaskSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $joinProcessor
     */
    public function __construct(
        TaskFactory $taskFactory,
        TaskResource $taskResource,
        CollectionFactory $collectionFactory,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        TaskSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $joinProcessor
    ) {
        $this->taskFactory = $taskFactory;
        $this->taskResource = $taskResource;
        $this->collectionFactory = $collectionFactory;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->joinProcessor = $joinProcessor;
    }

    /**
     * @param int $id
     * @return TaskInterface
     * @throws NoSuchEntityException
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
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): TaskSearchResultsInterface
    {
        if (!$searchCriteria) {
            $searchCriteria = $this->searchCriteriaBuilderFactory->create()->create();
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
     */
    public function deleteById(int $id): void
    {
        $this->delete($this->get($id));
    }
}
