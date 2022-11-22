<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider\Price;

use Magento\Catalog\Api\Data\ProductInterface;

interface PriceProviderInterface
{
    /**
     * @param ProductInterface $product
     * @param array $ignoredFields
     * @return array
     */
    public function getPrices(ProductInterface $product, array $ignoredFields) : array;
}
