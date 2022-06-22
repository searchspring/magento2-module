<?php

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
use SearchSpring\Feed\Api\Data\FeedInterface;
use SearchSpring\Feed\Api\Data\FeedSearchResultsInterface;
use SearchSpring\Feed\Api\Data\FeedSearchResultsInterfaceFactory;
use SearchSpring\Feed\Api\FeedRepositoryInterface;
use SearchSpring\Feed\Model\ResourceModel\Feed as FeedResource;
use SearchSpring\Feed\Model\ResourceModel\Feed\Collection;
use SearchSpring\Feed\Model\ResourceModel\Feed\CollectionFactory;

class FeedRepository implements FeedRepositoryInterface
{
    /**
     * @var FeedFactory
     */
    private $feedFactory;
    /**
     * @var FeedResource
     */
    private $feedResource;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var FeedSearchResultsInterfaceFactory
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
     * FeedRepository constructor.
     * @param FeedFactory $feedFactory
     * @param FeedResource $feedResource
     * @param CollectionFactory $collectionFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FeedSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $joinProcessor
     */
    public function __construct(
        FeedFactory $feedFactory,
        FeedResource $feedResource,
        CollectionFactory $collectionFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FeedSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $joinProcessor
    ) {
        $this->feedFactory = $feedFactory;
        $this->feedResource = $feedResource;
        $this->collectionFactory = $collectionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->joinProcessor = $joinProcessor;
    }

    /**
     * @param int $id
     * @return FeedInterface
     * @throws NoSuchEntityException
     */
    public function get(int $id): FeedInterface
    {
        /** @var Feed $feed */
        $feed = $this->feedFactory->create();
        $this->feedResource->load($feed, $id);
        if (!$feed->getEntityId()) {
            throw new NoSuchEntityException(__('The Feed with the "%1" ID doesn\'t exist.', $id));
        }

        return $feed;
    }

    /**
     * @param int $id
     * @return FeedInterface
     * @throws NoSuchEntityException
     */
    public function getByTaskId(int $id): FeedInterface
    {
        /** @var Feed $feed */
        $feed = $this->feedFactory->create();
        $this->feedResource->load($feed, $id, FeedInterface::TASK_ID);
        if (!$feed->getEntityId()) {
            throw new NoSuchEntityException(__('The Feed with the "%1" Task ID doesn\'t exist.', $id));
        }

        return $feed;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return FeedSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): FeedSearchResultsInterface
    {
        if (!$searchCriteria) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        }

        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->joinProcessor->process(
            $collection,
            FeedInterface::class
        );
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var FeedSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @param FeedInterface $feed
     * @return FeedInterface
     * @throws CouldNotSaveException
     */
    public function save(FeedInterface $feed): FeedInterface
    {
        try {
            $this->feedResource->save($feed);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()), $exception);
        }

        return $feed;
    }

    /**
     * @param FeedInterface $feed
     * @throws CouldNotDeleteException
     */
    public function delete(FeedInterface $feed): void
    {
        try {
            $this->feedResource->delete($feed);
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
