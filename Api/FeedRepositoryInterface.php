<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use SearchSpring\Feed\Api\Data\FeedInterface;
use SearchSpring\Feed\Api\Data\FeedSearchResultsInterface;

interface FeedRepositoryInterface
{
    /**
     * @param int $id
     * @return FeedInterface
     * @throws NoSuchEntityException
     */
    public function get(int $id) : FeedInterface;

    /**
     * @param int $id
     * @return FeedInterface
     * @throws NoSuchEntityException
     */
    public function getByTaskId(int $id) : FeedInterface;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return FeedSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria) : FeedSearchResultsInterface;

    /**
     * @param FeedInterface $feed
     * @return FeedInterface
     * @throws CouldNotSaveException
     */
    public function save(FeedInterface $feed) : FeedInterface;

    /**
     * @param FeedInterface $feed
     * @throws CouldNotDeleteException
     */
    public function delete(FeedInterface $feed) : void;

    /**
     * @param int $id
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById(int $id) : void;
}
