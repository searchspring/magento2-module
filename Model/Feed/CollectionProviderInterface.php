<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;

interface CollectionProviderInterface
{
    /**
     * @param FeedSpecificationInterface $specification
     * @return Collection
     */
    public function getCollection(FeedSpecificationInterface $specification) : Collection;
}
