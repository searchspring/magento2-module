<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Api\GenerateFeedInterface;
use SearchSpring\Feed\Model\Feed\Collection\ProcessorPool;
use SearchSpring\Feed\Model\Feed\CollectionConfigInterface;
use SearchSpring\Feed\Model\Feed\CollectionProviderInterface;
use SearchSpring\Feed\Model\Feed\ContextManagerInterface;
use SearchSpring\Feed\Model\Feed\DataProviderInterface;
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
     * @var SystemFieldsList
     */
    private $systemFieldsList;
    /**
     * @var ContextManagerInterface
     */
    private $contextManager;
    /**
     * @var ProcessorPool
     */
    private $afterLoadProcessorPool;

    /**
     * GenerateFeed constructor.
     * @param CollectionProviderInterface $collectionProvider
     * @param DataProviderPool $dataProviderPool
     * @param CollectionConfigInterface $collectionConfig
     * @param StorageInterface $storage
     * @param SystemFieldsList $systemFieldsList
     * @param ContextManagerInterface $contextManager
     * @param ProcessorPool $afterLoadProcessorPool
     */
    public function __construct(
        CollectionProviderInterface $collectionProvider,
        DataProviderPool $dataProviderPool,
        CollectionConfigInterface $collectionConfig,
        StorageInterface $storage,
        SystemFieldsList $systemFieldsList,
        ContextManagerInterface $contextManager,
        ProcessorPool $afterLoadProcessorPool
    ) {
        $this->collectionProvider = $collectionProvider;
        $this->dataProviderPool = $dataProviderPool;
        $this->collectionConfig = $collectionConfig;
        $this->storage = $storage;
        $this->systemFieldsList = $systemFieldsList;
        $this->contextManager = $contextManager;
        $this->afterLoadProcessorPool = $afterLoadProcessorPool;
    }

    /**
     * @param FeedSpecificationInterface $feedSpecification
     * @throws \Exception
     */
    public function execute(FeedSpecificationInterface $feedSpecification): void
    {
        $gcStatus = gc_enabled();
        if (!$gcStatus) {
            gc_enable();
        }

        $format = $feedSpecification->getFormat();
        if (!$this->storage->isSupportedFormat($format)) {
            throw new \Exception((string) __('%1 is not supported format'));
        }

        $this->resetDataProviders($feedSpecification);
        $this->contextManager->setContextFromSpecification($feedSpecification);
        $collection = $this->collectionProvider->getCollection($feedSpecification);
//        $collection->addAttributeToFilter('type_id', ['neq' => 'simple']);
        $pageSize = $this->collectionConfig->getPageSize();
        $collection->setPageSize($pageSize);
        $pageCount = $collection->getLastPageNumber();
//        $pageCount = 1;
        $currentPageNumber = 1;
        $memoryDumpPage = 1;
        $memoryDumpMaxPage = 10;
        $memoryDumps = 0;
        $data = [];
        $itemsDataSize = 0;
        $itemsDataCount = 0;
        $memoryDump['initial'] = [
            'usage' => memory_get_usage() / 1024 / 1024,
            'usage_real' => memory_get_usage(true) / 1024 / 1024,
            'peak' => memory_get_peak_usage() / 1024 / 1024,
            'peak_real' => memory_get_peak_usage(true) / 1024 / 1024,
            'full_data_size' => 0,
            'full_data_count' => 0,
            'items_data_size' => 0,
            'items_data_count' => 0
        ];
        while ($currentPageNumber <= $pageCount) {
            $collection->setCurPage($currentPageNumber);
            $collection->load();
            $this->processAfterLoad($collection, $feedSpecification);
            $itemsData = $this->getItemsData($collection->getItems(), $feedSpecification);
            $data = array_merge($data, $itemsData);
//            $data[] = serialize($itemsData);
            $itemsDataSize = round(mb_strlen(serialize($itemsData), '8bit') / 1024 / 1024, 4);
            $itemsDataCount = count($itemsData);
            unset($itemsData);
            $currentPageNumber++;
            gc_collect_cycles();
            $this->resetDataProvidersAfterFetchItems($feedSpecification);
            $collection->clear();
            $this->processAfterFetchItems($collection, $feedSpecification);
            if ($memoryDumpPage === $memoryDumpMaxPage) {
                $memoryDump[] = [
                    'usage' => round(memory_get_usage() / 1024 / 1024, 4),
                    'usage_real' => round(memory_get_usage(true) / 1024 / 1024, 4),
                    'peak' => round(memory_get_peak_usage() / 1024 / 1024, 4),
                    'peak_real' => round(memory_get_peak_usage(true) / 1024 / 1024, 4),
                    'full_data_size' => round(mb_strlen(serialize($data), '8bit') / 1024 / 1024, 4),
                    'full_data_count' => count($data),
                    'items_data_size' => $itemsDataSize,
                    'items_data_count' => $itemsDataCount
                ];
                $memoryDumpPage = 1;
                $this->printMemoryDump($memoryDump, $memoryDumpMaxPage * $pageSize * $memoryDumps, $memoryDumpMaxPage * $pageSize * ($memoryDumps + 1));
                $memoryDumps++;
                $data = [];
            } else {
                $memoryDumpPage++;
            }
        }

        $memoryDump[] = [
            'usage' => round(memory_get_usage() / 1024 / 1024, 4),
            'usage_real' => round(memory_get_usage(true) / 1024 / 1024, 4),
            'peak' => round(memory_get_peak_usage() / 1024 / 1024, 4),
            'peak_real' => round(memory_get_peak_usage(true) / 1024 / 1024, 4),
            'full_data_size' => round(mb_strlen(serialize($data), '8bit') / 1024 / 1024, 4),
            'full_data_count' => count($data),
            'items_data_size' => $itemsDataSize,
            'items_data_count' => $itemsDataCount
        ];
        $this->printMemoryDump($memoryDump);

        $this->storage->save($data, $feedSpecification);
        $this->contextManager->resetContext();
        if (!$gcStatus) {
            gc_disable();
        }
        return;
    }

    /**
     * @param array $dump
     * @param int|null $start
     * @param int|null $end
     */
    private function printMemoryDump(array $dump, int $start = null, int $end = null) : void
    {
        $memoryDumpView = [];
        foreach ($dump as $item) {
            foreach ($item as $key => $value) {
                $memoryDumpView[$key][] = $value;
            }
        }

        if (!$start && !$end) {
            $firstStr = '----- Final Result -----';
        } else {
            $start = is_null($start) ? 0 : $start;
            $firstStr = '----- Products ' . $start . ' - ' . $end . ' -----';
        }

        echo $firstStr . PHP_EOL;

        foreach ($memoryDumpView as $key => $item) {
            $valueStr = implode(' -> ', $item);
            $str = $key . ": " . $valueStr . PHP_EOL;
            echo $str;
        }
    }

    /**
     * @param Collection $collection
     * @param FeedSpecificationInterface $feedSpecification
     */
    private function processAfterLoad(Collection $collection, FeedSpecificationInterface $feedSpecification) : void
    {
        foreach ($this->afterLoadProcessorPool->getAll() as $processor) {
            $processor->processAfterLoad($collection, $feedSpecification);
        }
    }

    /**
     * @param Collection $collection
     * @param FeedSpecificationInterface $feedSpecification
     */
    private function processAfterFetchItems(Collection $collection, FeedSpecificationInterface $feedSpecification) : void
    {
        foreach ($this->afterLoadProcessorPool->getAll() as $processor) {
            $processor->processAfterFetchItems($collection, $feedSpecification);
        }
    }

    /**
     * @param FeedSpecificationInterface $feedSpecification
     */
    private function resetDataProviders(FeedSpecificationInterface $feedSpecification) : void
    {
        $dataProviders = $this->getDataProviders($feedSpecification);
        foreach ($dataProviders as $dataProvider) {
            $dataProvider->reset();
        }
    }

    /**
     * @param FeedSpecificationInterface $feedSpecification
     */
    private function resetDataProvidersAfterFetchItems(FeedSpecificationInterface $feedSpecification) : void
    {
        $dataProviders = $this->getDataProviders($feedSpecification);
        foreach ($dataProviders as $dataProvider) {
            $dataProvider->resetAfterFetchItems();
        }
    }

    /**
     * @param FeedSpecificationInterface $feedSpecification
     * @return DataProviderInterface[]
     */
    private function getDataProviders(FeedSpecificationInterface $feedSpecification) : array
    {
        $ignoredFields = $feedSpecification->getIgnoreFields();
        return $this->dataProviderPool->get($ignoredFields);
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
        $dataProviders = $this->getDataProviders($feedSpecification);
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
