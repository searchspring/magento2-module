<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Api;

use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;

interface GenerateFeedInterface
{
    /**
     * @param FeedSpecificationInterface $feedSpecification
     */
    public function execute(FeedSpecificationInterface $feedSpecification) : void;
}
