<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed;

use SearchSpring\Feed\Api\Data\FeedInterface;

interface StorageInterface
{
    /**
     * @param string $data
     * @param FeedInterface $feed
     * @return FeedInterface
     */
    public function save(string $data, FeedInterface $feed) : FeedInterface;
}
