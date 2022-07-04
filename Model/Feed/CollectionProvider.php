<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\Collection\ModifierInterface;

class CollectionProvider implements CollectionProviderInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var ModifierInterface[]
     */
    private $modifiers;

    /**
     * CollectionProvider constructor.
     * @param CollectionFactory $collectionFactory
     * @param array $modifiers
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        array $modifiers = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->modifiers = $modifiers;
    }

    /**
     * @param FeedSpecificationInterface $specification
     * @return Collection
     */
    public function getCollection(FeedSpecificationInterface $specification): Collection
    {
        $collection = $this->collectionFactory->create();
        foreach ($this->modifiers as $modifier) {
            $collection = $modifier->modify($collection, $specification);
        }

        return $collection;
    }
}
