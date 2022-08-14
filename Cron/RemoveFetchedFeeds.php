<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Cron;

use SearchSpring\Feed\Api\RemoveFetchedFeedsInterface;

class RemoveFetchedFeeds
{
    /**
     * @var RemoveFetchedFeedsInterface
     */
    private $removeFetchedFeeds;

    /**
     * RemoveFetchedFeeds constructor.
     * @param RemoveFetchedFeedsInterface $removeFetchedFeeds
     */
    public function __construct(
        RemoveFetchedFeedsInterface $removeFetchedFeeds
    ) {
        $this->removeFetchedFeeds = $removeFetchedFeeds;
    }

    /**
     *
     */
    public function execute() : void
    {
        $this->removeFetchedFeeds->execute();
    }
}
