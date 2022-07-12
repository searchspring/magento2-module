<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider\Stock;

use Magento\Framework\Exception\NoSuchEntityException;

interface StockResolverInterface
{
    /**
     * @return StockProviderInterface
     * @throws NoSuchEntityException
     */
    public function resolve() : StockProviderInterface;
}
