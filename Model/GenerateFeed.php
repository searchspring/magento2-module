<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model;

use SearchSpring\Feed\Api\Data\FeedInterface;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Api\GenerateFeedInterface;

class GenerateFeed implements GenerateFeedInterface
{

    /**
     * @param FeedSpecificationInterface $feedSpecification
     * @return FeedInterface
     */
    public function execute(FeedSpecificationInterface $feedSpecification): FeedInterface
    {
        // TODO: Implement execute() method.
    }
}
