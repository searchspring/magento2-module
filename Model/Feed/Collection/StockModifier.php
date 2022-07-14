<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\Collection;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\CatalogInventory\Helper\Stock;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;

class StockModifier implements ModifierInterface
{
    /**
     * @var Stock
     */
    private $stockHelper;

    /**
     * StockModifier constructor.
     * @param Stock $stockHelper
     */
    public function __construct(
        Stock $stockHelper
    ) {
        $this->stockHelper = $stockHelper;
    }

    /**
     * @param Collection $collection
     * @param FeedSpecificationInterface $feedSpecification
     * @return Collection
     */
    public function modify(Collection $collection, FeedSpecificationInterface $feedSpecification): Collection
    {
        if (!$feedSpecification->getIncludeOutOfStock()) {
            $this->stockHelper->addIsInStockFilterToCollection($collection);
        }

        return $collection;
    }
}
