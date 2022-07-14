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
use SearchSpring\Feed\Model\Feed\ContextManagerInterface;
use SearchSpring\Feed\Model\Feed\DataProviderPool;
use SearchSpring\Feed\Model\Feed\StorageInterface;
use SearchSpring\Feed\Model\Feed\SystemFieldsList;

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
     * @var SystemFieldsList
     */
    private $systemFieldsList;
    /**
     * @var ContextManagerInterface
     */
    private $contextManager;

    /**
     * GenerateFeed constructor.
     * @param CollectionProviderInterface $collectionProvider
     * @param DataProviderPool $dataProviderPool
     * @param CollectionConfigInterface $collectionConfig
     * @param StorageInterface $storage
     * @param FeedInterfaceFactory $feedFactory
     * @param SystemFieldsList $systemFieldsList
     * @param ContextManagerInterface $contextManager
     */
    public function __construct(
        CollectionProviderInterface $collectionProvider,
        DataProviderPool $dataProviderPool,
        CollectionConfigInterface $collectionConfig,
        StorageInterface $storage,
        FeedInterfaceFactory $feedFactory,
        SystemFieldsList $systemFieldsList,
        ContextManagerInterface $contextManager
    ) {
        $this->collectionProvider = $collectionProvider;
        $this->dataProviderPool = $dataProviderPool;
        $this->collectionConfig = $collectionConfig;
        $this->storage = $storage;
        $this->feedFactory = $feedFactory;
        $this->systemFieldsList = $systemFieldsList;
        $this->contextManager = $contextManager;
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

        $this->contextManager->setContextFromSpecification($feedSpecification);
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
        $this->contextManager->resetContext();
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

        $this->systemFieldsList->add('product_model');
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
        $systemFields = $this->systemFieldsList->get();
        foreach ($items as &$item) {
            foreach ($systemFields as $field) {
                if (isset($item[$field])) {
                    unset($item[$field]);
                }
            }
        }

        return $items;
    }
}
