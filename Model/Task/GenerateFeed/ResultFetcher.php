<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Task\GenerateFeed;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use SearchSpring\Feed\Api\Data\TaskInterface;
use SearchSpring\Feed\Api\FeedRepositoryInterface;
use SearchSpring\Feed\Model\Feed\StorageInterface;
use SearchSpring\Feed\Model\Task\ResultFetcherInterface;

class ResultFetcher implements ResultFetcherInterface
{
    /**
     * @var StorageInterface
     */
    private $storage;
    /**
     * @var FeedRepositoryInterface
     */
    private $feedRepository;

    /**
     * ResultFetcher constructor.
     * @param StorageInterface $storage
     * @param FeedRepositoryInterface $feedRepository
     */
    public function __construct(
        StorageInterface $storage,
        FeedRepositoryInterface $feedRepository
    ) {
        $this->storage = $storage;
        $this->feedRepository = $feedRepository;
    }

    /**
     * @param TaskInterface $task
     * @return mixed
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     */
    public function fetch(TaskInterface $task)
    {
        $feed = $this->feedRepository->getByTaskId($task->getEntityId());
        $wasFetched = $feed->getFetched();
        $rawData = $this->storage->getRawContent($feed);
        $filename = basename($feed->getFilePath());
        $result = [
            'format' => $feed->getFormat(),
            'name' => $filename,
            'data' => base64_encode($rawData)
        ];

        if (!$wasFetched) {
            $feed->setFetched(true);
            $this->storage->archive($feed);
            $this->feedRepository->save($feed);
        }

        return $result;
    }
}
