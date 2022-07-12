<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider\Stock;

interface StockProviderInterface
{
    /**
     * [
     *      product_id => [
     *          'qty' => float,
     *          'in_stock' => bool
     *      ],
     *      .........
     * ]
     *
     * @param array $productIds
     * @param int $storeId
     * @return array
     */
    public function getStock(array $productIds, int $storeId) : array;
}
