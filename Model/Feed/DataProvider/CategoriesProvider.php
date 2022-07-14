<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider;

use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\Category\CollectionBuilder;
use SearchSpring\Feed\Model\Feed\DataProviderInterface;

class CategoriesProvider implements DataProviderInterface
{
    private $loadedCategories = [];
    /**
     * @var CollectionBuilder
     */
    private $collectionBuilder;

    /**
     * CategoriesProvider constructor.
     * @param CollectionBuilder $collectionBuilder
     */
    public function __construct(
        CollectionBuilder $collectionBuilder
    ) {
        $this->collectionBuilder = $collectionBuilder;
    }

    /**
     * @param array $products
     * @param FeedSpecificationInterface $feedSpecification
     * @return array
     */
    public function getData(array $products, FeedSpecificationInterface $feedSpecification): array
    {
        $productIds = [];
        foreach ($products as $product) {
            if (isset($product['entity_id'])) {
                $productIds[] = (int) $product['entity_id'];
            }
        }

        if (empty($productIds)) {
            return [];
        }

        $loadedCategoryIds = array_keys($this->loadedCategories);
        $collection = $this->collectionBuilder->buildCollection($productIds, $feedSpecification, $loadedCategoryIds);
    }
}
