<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Task\GenerateFeed;

use Magento\Framework\Exception\CouldNotSaveException;
use SearchSpring\Feed\Api\Data\TaskInterface;
use SearchSpring\Feed\Api\GenerateFeedInterface;
use SearchSpring\Feed\Exception\GenericException;
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
     * Executor constructor.
     * @param SpecificationBuilderInterface $specificationBuilder
     * @param GenerateFeedInterface $generateFeed
     */
    public function __construct(
        SpecificationBuilderInterface $specificationBuilder,
        GenerateFeedInterface $generateFeed
    ) {
        $this->specificationBuilder = $specificationBuilder;
        $this->generateFeed = $generateFeed;
    }

    /**
     * @param TaskInterface $task
     * @return void
     * @throws GenericException
     */
    public function execute(TaskInterface $task)
    {
        $specification = $this->specificationBuilder->build($task->getPayload());
        $this->generateFeed->execute($specification);
    }
}
