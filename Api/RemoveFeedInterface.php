<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Api;

use Magento\Framework\Exception\CouldNotSaveException;
use SearchSpring\Feed\Api\Data\FeedInterface;
use Throwable;

interface RemoveFeedInterface
{
    /**
     * @param FeedInterface $feed
     * @return FeedInterface
     * @throws CouldNotSaveException
     * @throws Throwable
     */
    public function execute(FeedInterface $feed) : FeedInterface;
}
