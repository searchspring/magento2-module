<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider\Price;

use Magento\Catalog\Api\Data\ProductInterface;

interface ProviderResolverInterface
{
    /**
     * @param ProductInterface $product
     * @return PriceProviderInterface
     */
    public function resolve(ProductInterface $product) : PriceProviderInterface;
}
