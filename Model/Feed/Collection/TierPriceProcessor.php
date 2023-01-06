<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\Collection;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;

class TierPriceProcessor implements ProcessCollectionInterface
{

    /**
     * @param Collection $collection
     * @param FeedSpecificationInterface $feedSpecification
     * @return Collection
     */
    public function processAfterLoad(Collection $collection, FeedSpecificationInterface $feedSpecification): Collection
    {
        if ($feedSpecification->getIncludeTierPricing()) {
            $collection->addTierPriceData();
        }

        return $collection;
    }

    /**
     * @param Collection $collection
     * @param FeedSpecificationInterface $feedSpecification
     * @return Collection
     */
    public function processAfterFetchItems(Collection $collection, FeedSpecificationInterface $feedSpecification): Collection
    {
        if ($feedSpecification->getIncludeTierPricing()) {
            $collection->setFlag('tier_price_added', false);
        }

        return $collection;
    }
}
