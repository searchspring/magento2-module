<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Api;

use SearchSpring\Feed\Api\Data\FeedInterface;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;

interface GenerateFeedInterface
{
    /**
     * @param FeedSpecificationInterface $feedSpecification
     * @return FeedInterface
     */
    public function execute(FeedSpecificationInterface $feedSpecification) : FeedInterface;
}
