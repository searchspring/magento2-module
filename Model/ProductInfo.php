<?php
/**
 * Copyright (C) 2026 Searchspring <https://searchspring.com>
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace SearchSpring\Feed\Model;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Api\Data\ProductInfoResponseInterface;
use SearchSpring\Feed\Api\Data\ProductInfoResponseInterfaceFactory;
use SearchSpring\Feed\Api\ProductInfoInterface;
use SearchSpring\Feed\Model\Feed\Collection\ProcessorPool;
use SearchSpring\Feed\Model\Feed\CollectionProviderInterface;
use SearchSpring\Feed\Model\Feed\ContextManagerInterface;
use SearchSpring\Feed\Model\Feed\DataProviderInterface;
use SearchSpring\Feed\Model\Feed\DataProviderPool;
use SearchSpring\Feed\Model\Feed\SpecificationBuilderInterface;
use SearchSpring\Feed\Model\Feed\SystemFieldsList;
use SearchSpring\Feed\Model\Task\TaskPayloadProvider;

/**
 * Generates the feed rows of a single product (and its parents) the same way GenerateFeed does,
 * without writing any file. Helps to find out why a product is missing or has wrong data in the feed.
 */
class ProductInfo implements ProductInfoInterface
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
     * @var ProcessorPool
     */
    private $afterLoadProcessorPool;
    /**
     * @var SystemFieldsList
     */
    private $systemFieldsList;
    /**
     * @var ContextManagerInterface
     */
    private $contextManager;
    /**
     * @var SpecificationBuilderInterface
     */
    private $specificationBuilder;
    /**
     * @var TaskPayloadProvider
     */
    private $taskPayloadProvider;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var Configurable
     */
    private $configurableType;
    /**
     * @var Grouped
     */
    private $groupedType;
    /**
     * @var ProductInfoResponseInterfaceFactory
     */
    private $responseFactory;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param CollectionProviderInterface $collectionProvider
     * @param DataProviderPool $dataProviderPool
     * @param ProcessorPool $afterLoadProcessorPool
     * @param SystemFieldsList $systemFieldsList
     * @param ContextManagerInterface $contextManager
     * @param SpecificationBuilderInterface $specificationBuilder
     * @param TaskPayloadProvider $taskPayloadProvider
     * @param StoreManagerInterface $storeManager
     * @param Configurable $configurableType
     * @param Grouped $groupedType
     * @param ProductInfoResponseInterfaceFactory $responseFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        CollectionProviderInterface $collectionProvider,
        DataProviderPool $dataProviderPool,
        ProcessorPool $afterLoadProcessorPool,
        SystemFieldsList $systemFieldsList,
        ContextManagerInterface $contextManager,
        SpecificationBuilderInterface $specificationBuilder,
        TaskPayloadProvider $taskPayloadProvider,
        StoreManagerInterface $storeManager,
        Configurable $configurableType,
        Grouped $groupedType,
        ProductInfoResponseInterfaceFactory $responseFactory,
        LoggerInterface $logger
    ) {
        $this->collectionProvider = $collectionProvider;
        $this->dataProviderPool = $dataProviderPool;
        $this->afterLoadProcessorPool = $afterLoadProcessorPool;
        $this->systemFieldsList = $systemFieldsList;
        $this->contextManager = $contextManager;
        $this->specificationBuilder = $specificationBuilder;
        $this->taskPayloadProvider = $taskPayloadProvider;
        $this->storeManager = $storeManager;
        $this->configurableType = $configurableType;
        $this->groupedType = $groupedType;
        $this->responseFactory = $responseFactory;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function getInfo(int $productId, int $storeId = 1): ProductInfoResponseInterface
    {
        /** @var ProductInfoResponseInterface $response */
        $response = $this->responseFactory->create();
        $response->setProductInfo([]);
        $productIds = [$productId];

        try {
            $storeCode = $this->storeManager->getStore($storeId)->getCode();
            $productIds = $this->getProductAndParentIds($productId);
            $response->setProductIds($productIds)->setStoreCode($storeCode);

            $task = $this->taskPayloadProvider->getLatestTaskForStore($storeCode);
            if (!$task) {
                return $response->setMessage('No feed generation task found, a task payload is required to build the feed specification');
            }

            $payload = $task->getPayload();
            $messages = [];
            if (($payload['store'] ?? null) !== $storeCode) {
                $messages[] = sprintf(
                    'No task found for store "%s", payload of task %d (store "%s") is used with the store replaced.',
                    $storeCode,
                    $task->getEntityId(),
                    $payload['store'] ?? ''
                );
                $payload['store'] = $storeCode;
            }

            $response->setTaskId((int) $task->getEntityId());
            $feedSpecification = $this->specificationBuilder->build($payload);
            $this->logger->info('ProductInfoAPI started', [
                'method' => __METHOD__,
                'productIds' => $productIds,
                'storeCode' => $storeCode,
                'taskId' => $task->getEntityId(),
            ]);

            $itemsData = $this->generate($feedSpecification, $productIds, $response);
            $response->setProductInfo($itemsData);
            if (!$itemsData) {
                $messages[] = 'Product is not part of the feed collection, it is excluded by a collection modifier'
                    . ' (store, status, visibility, stock) or does not exist. Check "query".';
            }

            $foundIds = array_map('intval', array_column($itemsData, 'entity_id'));
            if ($itemsData && !in_array($productId, $foundIds, true)) {
                $messages[] = 'Requested product is not part of the feed collection itself, only its parent(s) are.';
            }

            $response->setMessage($messages ? implode(' ', $messages) : null);
        } catch (\Throwable $exception) {
            $this->logger->error('ProductInfoAPI failed', [
                'method' => __METHOD__,
                'productIds' => $productIds,
                'storeId' => $storeId,
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
            $response->setProductInfo([])->setMessage($exception->getMessage());
        }

        return $response;
    }

    /**
     * Mirrors GenerateFeed::execute() for a single page restricted to the given product ids.
     *
     * @param FeedSpecificationInterface $feedSpecification
     * @param int[] $productIds
     * @param ProductInfoResponseInterface $response
     * @return array
     * @throws \Exception
     */
    private function generate(
        FeedSpecificationInterface $feedSpecification,
        array $productIds,
        ProductInfoResponseInterface $response
    ): array {
        $dataProviders = $this->dataProviderPool->get($feedSpecification->getIgnoreFields());
        $this->resetDataProviders($dataProviders);
        $this->contextManager->setContextFromSpecification($feedSpecification);
        try {
            $collection = $this->collectionProvider->getCollection($feedSpecification);
            $collection->addFieldToFilter('entity_id', ['in' => $productIds]);
            $response->setQuery($collection->getSelect()->__toString());
            $collection->load();
            $this->processAfterLoad($collection, $feedSpecification);

            $items = $collection->getItems();
            if (!$items) {
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
            foreach ($dataProviders as $dataProvider) {
                $data = $dataProvider->getData($data, $feedSpecification);
            }

            foreach ($dataProviders as $dataProvider) {
                $dataProvider->resetAfterFetchItems();
            }
            $collection->clear();
            $this->processAfterFetchItems($collection, $feedSpecification);

            return $this->cleanupItemsData($data);
        } finally {
            $this->resetDataProviders($dataProviders);
            $this->contextManager->resetContext();
        }
    }

    /**
     * Parents are added since children which are not visible individually are only part of the feed through them.
     *
     * @param int $productId
     * @return int[]
     */
    private function getProductAndParentIds(int $productId): array
    {
        $parentIds = array_merge(
            $this->configurableType->getParentIdsByChild($productId),
            $this->groupedType->getParentIdsByChild($productId)
        );

        return array_values(array_unique(array_map('intval', array_merge([$productId], $parentIds))));
    }

    /**
     * @param DataProviderInterface[] $dataProviders
     * @return void
     */
    private function resetDataProviders(array $dataProviders): void
    {
        foreach ($dataProviders as $dataProvider) {
            $dataProvider->reset();
        }
    }

    /**
     * @param Collection $collection
     * @param FeedSpecificationInterface $feedSpecification
     * @return void
     */
    private function processAfterLoad(Collection $collection, FeedSpecificationInterface $feedSpecification): void
    {
        foreach ($this->afterLoadProcessorPool->getAll() as $processor) {
            $processor->processAfterLoad($collection, $feedSpecification);
        }
    }

    /**
     * @param Collection $collection
     * @param FeedSpecificationInterface $feedSpecification
     * @return void
     */
    private function processAfterFetchItems(Collection $collection, FeedSpecificationInterface $feedSpecification): void
    {
        foreach ($this->afterLoadProcessorPool->getAll() as $processor) {
            $processor->processAfterFetchItems($collection, $feedSpecification);
        }
    }

    /**
     * @param array $items
     * @return array
     */
    private function cleanupItemsData(array $items): array
    {
        $systemFields = $this->systemFieldsList->get();
        foreach ($items as &$item) {
            foreach ($systemFields as $field) {
                unset($item[$field]);
            }
        }

        return $items;
    }
}
