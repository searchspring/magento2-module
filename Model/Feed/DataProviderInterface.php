<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed;

use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;

interface DataProviderInterface
{
    /**
     * @param array $products
     * @param FeedSpecificationInterface $feedSpecification
     * @return array
     */
    public function getData(array $products, FeedSpecificationInterface $feedSpecification) : array;

    /**
     *
     */
    public function reset() : void;
}
