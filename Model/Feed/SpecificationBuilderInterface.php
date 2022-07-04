<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed;

use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;

interface SpecificationBuilderInterface
{
    /**
     * @param array $data
     * @return FeedSpecificationInterface
     */
    public function build(array $data) : FeedSpecificationInterface;
}
