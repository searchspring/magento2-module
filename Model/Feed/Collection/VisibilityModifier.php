<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\Collection;

use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;

class VisibilityModifier implements ModifierInterface
{
    /**
     * @var Visibility
     */
    private $visibility;

    /**
     * VisibilityModifier constructor.
     * @param Visibility $visibility
     */
    public function __construct(
        Visibility $visibility
    ) {
        $this->visibility = $visibility;
    }

    /**
     * @param Collection $collection
     * @param FeedSpecificationInterface $feedSpecification
     * @return Collection
     */
    public function modify(Collection $collection, FeedSpecificationInterface $feedSpecification): Collection
    {
        $collection->setVisibility($this->visibility->getVisibleInSiteIds());
        return $collection;
    }
}
