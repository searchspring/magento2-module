<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Api;

use Exception;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Exception\GenericException;

interface GenerateFeedInterface
{
    /**
     * @param FeedSpecificationInterface $feedSpecification
     * @throws GenericException
     * @throws Exception
     */
    public function execute(FeedSpecificationInterface $feedSpecification) : void;
}
