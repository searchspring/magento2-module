<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider\Category;

use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;

class CollectionBuilder
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * CollectionBuilder constructor.
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param array $productIds
     * @param FeedSpecificationInterface $feedSpecification
     * @param array $excludedCategoryIds
     * @return Collection
     */
    public function buildCollection(
        array $productIds,
        FeedSpecificationInterface $feedSpecification,
        array $excludedCategoryIds
    ) : Collection {
        $productCategories = $this->resolveProductsCategories($productIds);
    }

    private function resolveProductsCategories(array $productIds) : array
    {

    }
}
