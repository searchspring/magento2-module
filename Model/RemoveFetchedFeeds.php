<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use SearchSpring\Feed\Api\Data\FeedInterface;
use SearchSpring\Feed\Api\FeedRepositoryInterface;
use SearchSpring\Feed\Api\RemoveFeedInterface;
use SearchSpring\Feed\Api\RemoveFetchedFeedsInterface;

class RemoveFetchedFeeds implements RemoveFetchedFeedsInterface
{
    /**
     * @var FeedRepositoryInterface
     */
    private $feedRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var RemoveFeedInterface
     */
    private $removeFeed;

    /**
     * RemoveFetchedFeeds constructor.
     * @param FeedRepositoryInterface $feedRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RemoveFeedInterface $removeFeed
     * @param LoggerInterface $logger
     */
    public function __construct(
        FeedRepositoryInterface $feedRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RemoveFeedInterface $removeFeed,
        LoggerInterface $logger
    ) {
        $this->feedRepository = $feedRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->logger = $logger;
        $this->removeFeed = $removeFeed;
    }

    /**
     * return removed feed count
     * @return int
     * @throws LocalizedException
     */
    public function execute(): int
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(FeedInterface::FETCHED, 1)
            ->addFilter(FeedInterface::FILE_DELETED, 0)
            ->create();
        $count = 0;
        $feeds = $this->feedRepository->getList($searchCriteria)->getItems();
        foreach ($feeds as $feed) {
            try {
                $this->removeFeed->execute($feed);
                $count++;
            } catch (\Throwable $exception) {
                $this->logger->error($exception->getMessage(), ['trace' => $exception->getTraceAsString()]);
            }
        }

        return $count;
    }
}
