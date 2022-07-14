<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed;

use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;

interface ContextManagerInterface
{
    /**
     * @param FeedSpecificationInterface $feedSpecification
     */
    public function setContextFromSpecification(FeedSpecificationInterface $feedSpecification) : void;

    /**
     *
     */
    public function resetContext() : void;
}
