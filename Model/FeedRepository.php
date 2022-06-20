<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use SearchSpring\Feed\Api\Data\FeedInterface;
use SearchSpring\Feed\Api\Data\FeedSearchResultsInterface;
use SearchSpring\Feed\Api\FeedRepositoryInterface;

class FeedRepository implements FeedRepositoryInterface
{

    /**
     * @param int $id
     * @return FeedInterface
     * @throws NoSuchEntityException
     */
    public function get(int $id): FeedInterface
    {
        // TODO: Implement get() method.
    }

    /**
     * @param int $id
     * @return FeedInterface
     * @throws NoSuchEntityException
     */
    public function getByTaskId(int $id): FeedInterface
    {
        // TODO: Implement getByTaskId() method.
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return FeedSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): FeedSearchResultsInterface
    {
        // TODO: Implement getList() method.
    }

    /**
     * @param FeedInterface $feed
     * @return FeedInterface
     * @throws CouldNotSaveException
     */
    public function save(FeedInterface $feed): FeedInterface
    {
        // TODO: Implement save() method.
    }

    /**
     * @param FeedInterface $feed
     * @throws CouldNotDeleteException
     */
    public function delete(FeedInterface $feed): void
    {
        // TODO: Implement delete() method.
    }

    /**
     * @param int $id
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById(int $id): void
    {
        // TODO: Implement deleteById() method.
    }
}
