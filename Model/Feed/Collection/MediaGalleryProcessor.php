<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\Collection;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Exception\LocalizedException;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;

class MediaGalleryProcessor implements ProcessCollectionInterface
{
    /**
     * @param Collection $collection
     * @param FeedSpecificationInterface $feedSpecification
     * @return Collection
     * @throws LocalizedException
     */
    public function processAfterLoad(Collection $collection, FeedSpecificationInterface $feedSpecification): Collection
    {
        if ($feedSpecification->getMediaGallerySpecification()->getIncludeMediaGallery()) {
            $collection->addMediaGalleryData();
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
        if ($feedSpecification->getMediaGallerySpecification()->getIncludeMediaGallery()) {
            $collection->setFlag('media_gallery_added', false);
        }

        return $collection;
    }
}
