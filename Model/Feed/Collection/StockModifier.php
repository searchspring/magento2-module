<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\Collection;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\CatalogInventory\Model\ResourceModel\Stock\Status;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;

class StockModifier implements ModifierInterface
{
    /**
     * @var Status
     */
    private $status;

    /**
     * StockModifier constructor.
     * @param Status $status
     */
    public function __construct(
        Status $status
    ) {
        $this->status = $status;
    }

    /**
     * @param Collection $collection
     * @param FeedSpecificationInterface $feedSpecification
     * @return Collection
     */
    public function modify(Collection $collection, FeedSpecificationInterface $feedSpecification): Collection
    {
        $includeOutOfStock = $feedSpecification->getIncludeOutOfStock();
        $stockFlag = 'has_stock_status_filter';
        if (!$collection->hasFlag($stockFlag)) {
            $this->status->addStockDataToCollection(
                $collection,
                !$includeOutOfStock
            );
            $collection->setFlag($stockFlag, true);
        }

        return $collection;
    }
}
