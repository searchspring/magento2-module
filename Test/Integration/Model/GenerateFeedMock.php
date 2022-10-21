<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Test\Integration\Model;

use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Api\GenerateFeedInterface;

class GenerateFeedMock implements GenerateFeedInterface
{

    /**
     * @param FeedSpecificationInterface $feedSpecification
     */
    public function execute(FeedSpecificationInterface $feedSpecification): void
    {
        return;
    }
}
