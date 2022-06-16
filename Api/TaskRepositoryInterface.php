<?php

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
     */
    public function get(int $id) : TaskInterface;

    /**
     * @param SearchCriteriaInterface|null $searchCriteria
     * @return TaskSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null) : TaskSearchResultsInterface;

    /**
     * @param TaskInterface $task
     * @return TaskInterface
     * @throws CouldNotSaveException
     */
    public function save(TaskInterface $task) : TaskInterface;

    /**
     * @param TaskInterface $task
     * @throws CouldNotDeleteException
     */
    public function delete(TaskInterface $task) : void;

    /**
     * @param int $id
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById(int $id) : void;
}
