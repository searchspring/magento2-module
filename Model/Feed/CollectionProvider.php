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
     * @throws \Exception
     */
    public function getCollection(FeedSpecificationInterface $specification): Collection
    {
        $collection = $this->collectionFactory->create();
        $modifiers = $this->sort($this->modifiers);
        foreach ($modifiers as $key => $modifierData) {
            /** @var ModifierInterface $modifier */
            $modifier = $modifierData['objectInstance'] ?? null;
            if (!$modifier) {
                throw new \Exception((string) __('No objectInstance for modifier %1', $key));
            }
            $collection = $modifier->modify($collection, $specification);
        }

        return $collection;
    }

    /**
     * @param array $data
     * @return array
     */
    private function sort(array $data)
    {
        usort($data, function (array $a, array $b) {
            return $this->getSortOrder($a) <=> $this->getSortOrder($b);
        });

        return $data;
    }

    /**
     * @param array $variable
     * @return int
     */
    private function getSortOrder(array $variable)
    {
        return !empty($variable['sortOrder']) ? (int) $variable['sortOrder'] : 0;
    }
}
