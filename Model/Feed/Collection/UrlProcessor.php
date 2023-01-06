<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\Collection;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;

class UrlProcessor implements ProcessCollectionInterface
{
    /**
     * @param Collection $collection
     * @param FeedSpecificationInterface $feedSpecification
     * @return Collection
     */
    public function processAfterLoad(Collection $collection, FeedSpecificationInterface $feedSpecification): Collection
    {
        $ignoredFields = $feedSpecification->getIgnoreFields();
        if (!in_array('url', $ignoredFields)) {
            $collection->addUrlRewrite();
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
        return $collection;
    }
}
