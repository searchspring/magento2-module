<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Task\GenerateFeed;

use Magento\Framework\Exception\CouldNotSaveException;
use SearchSpring\Feed\Api\Data\TaskInterface;
use SearchSpring\Feed\Api\FeedRepositoryInterface;
use SearchSpring\Feed\Api\GenerateFeedInterface;
use SearchSpring\Feed\Model\Feed\SpecificationBuilderInterface;
use SearchSpring\Feed\Model\Task\ExecutorInterface;

class Executor implements ExecutorInterface
{
    /**
     * @var SpecificationBuilderInterface
     */
    private $specificationBuilder;
    /**
     * @var GenerateFeedInterface
     */
    private $generateFeed;
    /**
     * @var FeedRepositoryInterface
     */
    private $feedRepository;

    /**
     * Executor constructor.
     * @param SpecificationBuilderInterface $specificationBuilder
     * @param GenerateFeedInterface $generateFeed
     * @param FeedRepositoryInterface $feedRepository
     */
    public function __construct(
        SpecificationBuilderInterface $specificationBuilder,
        GenerateFeedInterface $generateFeed,
        FeedRepositoryInterface $feedRepository
    ) {
        $this->specificationBuilder = $specificationBuilder;
        $this->generateFeed = $generateFeed;
        $this->feedRepository = $feedRepository;
    }

    /**
     * @param TaskInterface $task
     * @return mixed
     * @throws CouldNotSaveException
     */
    public function execute(TaskInterface $task)
    {
        $specification = $this->specificationBuilder->build($task->getPayload());
        $feed = $this->generateFeed->execute($specification);
        $feed->setTaskId($task->getEntityId());
        $this->feedRepository->save($feed);

        return $feed;
    }
}
