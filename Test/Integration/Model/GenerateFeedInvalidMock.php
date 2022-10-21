<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Test\Integration\Model;

use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Api\GenerateFeedInterface;
use SearchSpring\Feed\Exception\GenericException;

class GenerateFeedInvalidMock implements GenerateFeedInterface
{

    /**
     * @param FeedSpecificationInterface $feedSpecification
     * @throws GenericException
     */
    public function execute(FeedSpecificationInterface $feedSpecification): void
    {
        throw new GenericException('exception message');
    }
}
