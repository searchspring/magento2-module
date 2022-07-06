<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed;

use SearchSpring\Feed\Api\Data\FeedInterface;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;

interface StorageInterface
{
    /**
     * @param array $data
     * @param FeedInterface $feed
     * @param FeedSpecificationInterface $feedSpecification
     * @return FeedInterface
     */
    public function save(array $data, FeedInterface $feed, FeedSpecificationInterface $feedSpecification) : FeedInterface;

    /**
     * @param string $format
     * @return bool
     */
    public function isSupportedFormat(string $format) : bool;

    /**
     * @param FeedInterface $feed
     */
    public function archive(FeedInterface $feed) : void;

    /**
     * @param FeedInterface $feed
     * @return string
     */
    public function getRawContent(FeedInterface $feed) : string;
}
