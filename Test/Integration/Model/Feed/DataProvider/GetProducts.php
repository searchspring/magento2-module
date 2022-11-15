<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Test\Integration\Model\Feed\DataProvider;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\Collection\ProcessorPool;
use SearchSpring\Feed\Model\Feed\CollectionProviderInterface;
use Magento\TestFramework\Helper\Bootstrap;


class GetProducts
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var CollectionProviderInterface
     */
    private $collectionProvider;

    /**
     * GetProducts constructor.
     * @param CollectionProviderInterface $collectionProvider
     */
    public function __construct(
        CollectionProviderInterface $collectionProvider
    ) {
        $this->collectionProvider = $collectionProvider;
    }

    /**
     * @param FeedSpecificationInterface $specification
     * @return array
     */
    public function get(FeedSpecificationInterface $specification) : array
    {
        $collection = $this->collectionProvider->getCollection($specification);
        /** @var ProcessorPool $processorPool */
        $processorPool = Bootstrap::getObjectManager()->get('SearchSpringFeedGenerateFeedAfterLoadCollectionProcessorPool');
        foreach ($processorPool->getAll() as $processor) {
            $processor->process($collection, $specification);
        }

        $result = [];
        foreach ($collection as $item) {
            $result[] = [
                'entity_id' => $item->getEntityId(),
                'product_model' => $item
            ];
        }

        return $result;
    }
}
