<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model;

use Magento\Catalog\Model\Product;
use SearchSpring\Feed\Api\Data\FeedInterface;
use SearchSpring\Feed\Api\Data\FeedInterfaceFactory;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Api\GenerateFeedInterface;
use SearchSpring\Feed\Model\Feed\CollectionConfigInterface;
use SearchSpring\Feed\Model\Feed\CollectionProviderInterface;
use SearchSpring\Feed\Model\Feed\DataProviderPool;
use SearchSpring\Feed\Model\Feed\StorageInterface;

class GenerateFeed implements GenerateFeedInterface
{
    /**
     * @var CollectionProviderInterface
     */
    private $collectionProvider;
    /**
     * @var DataProviderPool
     */
    private $dataProviderPool;
    /**
     * @var CollectionConfigInterface
     */
    private $collectionConfig;
    /**
     * @var StorageInterface
     */
    private $storage;
    /**
     * @var FeedInterfaceFactory
     */
    private $feedFactory;
    /**
     * @var array
     */
    private $itemSystemFields = [
        'product_model'
    ];

    /**
     * GenerateFeed constructor.
     * @param CollectionProviderInterface $collectionProvider
     * @param DataProviderPool $dataProviderPool
     * @param CollectionConfigInterface $collectionConfig
     * @param StorageInterface $storage
     * @param FeedInterfaceFactory $feedFactory
     * @param array $itemSystemFields
     */
    public function __construct(
        CollectionProviderInterface $collectionProvider,
        DataProviderPool $dataProviderPool,
        CollectionConfigInterface $collectionConfig,
        StorageInterface $storage,
        FeedInterfaceFactory $feedFactory,
        array $itemSystemFields = []
    ) {
        $this->collectionProvider = $collectionProvider;
        $this->dataProviderPool = $dataProviderPool;
        $this->collectionConfig = $collectionConfig;
        $this->storage = $storage;
        $this->feedFactory = $feedFactory;
        $this->itemSystemFields = array_merge($this->itemSystemFields, $itemSystemFields);
    }

    /**
     * @param FeedSpecificationInterface $feedSpecification
     * @return FeedInterface
     * @throws \Exception
     */
    public function execute(FeedSpecificationInterface $feedSpecification): FeedInterface
    {
        $format = $feedSpecification->getFormat();
        if (!$this->storage->isSupportedFormat($format)) {
            throw new \Exception();
        }

        $collection = $this->collectionProvider->getCollection($feedSpecification);
        $pageSize = $this->collectionConfig->getPageSize();
        $collection->setPageSize($pageSize);
        $pageCount = $collection->getLastPageNumber();
        $currentPageNumber = 1;
        $data = [];
        while ($currentPageNumber <= $pageCount) {
            $collection->clear();
            $collection->setCurPage($currentPageNumber);
            $itemsData = $this->getItemsData($collection->getItems(), $feedSpecification);
            $data = array_merge($data, $itemsData);
            $currentPageNumber++;
        }

        /** @var FeedInterface $feed */
        $feed = $this->feedFactory->create();
        $feed->setFetched(false)
            ->setFormat($format);

        $this->storage->save($data, $feed, $feedSpecification);
        return $feed;
    }

    /**
     * @param Product[] $items
     * @param FeedSpecificationInterface $feedSpecification
     * @return array
     */
    private function getItemsData(array $items, FeedSpecificationInterface $feedSpecification) : array
    {
        if (empty($items)) {
            return [];
        }

        $data = [];
        foreach ($items as $item) {
            $data[] = [
                'entity_id' => $item->getEntityId(),
                'product_model' => $item
            ];
        }

        $ignoredFields = $feedSpecification->getIgnoreFields();
        $dataProviders = $this->dataProviderPool->get($ignoredFields);
        foreach ($dataProviders as $dataProvider) {
            $data = $dataProvider->getData($data, $feedSpecification);
        }

        $data = $this->cleanupItemsData($data);
        return $data;
    }

    /**
     * @param array $items
     * @return array
     */
    private function cleanupItemsData(array $items) : array
    {
        foreach ($items as &$item) {
            foreach ($this->itemSystemFields as $field) {
                if (isset($item[$field])) {
                    unset($item[$field]);
                }
            }
        }

        return $items;
    }
}
