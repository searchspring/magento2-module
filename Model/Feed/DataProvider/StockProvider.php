<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\Stock\StockResolverInterface;
use SearchSpring\Feed\Model\Feed\DataProviderInterface;

class StockProvider implements DataProviderInterface
{
    /**
     * @var StockResolverInterface
     */
    private $stockResolver;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * StockProvider constructor.
     * @param StockResolverInterface $stockResolver
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StockResolverInterface $stockResolver,
        StoreManagerInterface $storeManager
    ) {
        $this->stockResolver = $stockResolver;
        $this->storeManager = $storeManager;
    }

    /**
     * @param array $products
     * @param FeedSpecificationInterface $feedSpecification
     * @return array
     * @throws NoSuchEntityException
     */
    public function getData(array $products, FeedSpecificationInterface $feedSpecification): array
    {
        $ignoreFields = $feedSpecification->getIgnoreFields();
        $productIds = [];
        foreach ($products as $product) {
            if (isset($product['entity_id'])) {
                $productIds[] = (int) $product['entity_id'];
            }
        }

        if (empty($productIds)) {
            return $products;
        }

        $stockProvider = $this->stockResolver->resolve();
        $storeId = (int) $this->storeManager->getStore($feedSpecification->getStoreCode())->getId();
        $stockData = $stockProvider->getStock($productIds, $storeId);
        foreach ($products as &$product) {
            $productId = $product['entity_id'] ?? null;
            if ($productId && isset($stockData[$productId])) {
                $stockItem = $stockData[$productId];
                if (!in_array('in_stock', $ignoreFields) && isset($stockItem['in_stock'])) {
                    $product['in_stock'] = (int) $stockItem['in_stock'];
                }

                if (!in_array('stock_qty', $ignoreFields) && isset($stockItem['qty'])) {
                    $product['stock_qty'] = (float) $stockItem['qty'];
                }
            }
        }

        return $products;
    }
}
