<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\Collection;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\CatalogRule\Model\ResourceModel\Product\CollectionProcessor;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\PricesProvider;

class PricesModifier implements ModifierInterface
{
    /**
     * @var CollectionProcessor
     */
    private $collectionProcessor;

    /**
     * PricesModifier constructor.
     * @param CollectionProcessor $collectionProcessor
     */
    public function __construct(
        CollectionProcessor $collectionProcessor
    ) {
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @param Collection $collection
     * @param FeedSpecificationInterface $feedSpecification
     * @return Collection
     */
    public function modify(Collection $collection, FeedSpecificationInterface $feedSpecification): Collection
    {
        $ignoredFields = $feedSpecification->getIgnoreFields();
        if (!in_array(PricesProvider::FINAL_PRICE_KEY, $ignoredFields)
            || !in_array(PricesProvider::MAX_PRICE_KEY, $ignoredFields)
            || !in_array(PricesProvider::REGULAR_PRICE_KEY, $ignoredFields)
        ) {
            $collection->addPriceData();
            $this->collectionProcessor->addPriceData($collection);
        }

        return $collection;
    }
}
