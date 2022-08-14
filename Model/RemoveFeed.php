<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\CouldNotSaveException;
use SearchSpring\Feed\Api\Data\FeedInterface;
use SearchSpring\Feed\Api\FeedRepositoryInterface;
use SearchSpring\Feed\Api\RemoveFeedInterface;
use SearchSpring\Feed\Model\Feed\StorageInterface;

class RemoveFeed implements RemoveFeedInterface
{
    /**
     * @var FeedRepositoryInterface
     */
    private $feedRepository;
    /**
     * @var StorageInterface
     */
    private $storage;
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * RemoveFeed constructor.
     * @param FeedRepositoryInterface $feedRepository
     * @param StorageInterface $storage
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        FeedRepositoryInterface $feedRepository,
        StorageInterface $storage,
        ResourceConnection $resourceConnection
    ) {
        $this->feedRepository = $feedRepository;
        $this->storage = $storage;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param FeedInterface $feed
     * @return FeedInterface
     * @throws CouldNotSaveException
     * @throws \Throwable
     */
    public function execute(FeedInterface $feed): FeedInterface
    {
        $connection = $this->resourceConnection->getConnection();
        $connection->beginTransaction();
        try {
            $feed->setFileDeleted(true);
            $this->feedRepository->save($feed);
            $this->storage->delete($feed);
            $connection->commit();
        } catch (\Throwable $exception) {
            $connection->rollBack();
            throw $exception;
        }

        return $feed;
    }
}
