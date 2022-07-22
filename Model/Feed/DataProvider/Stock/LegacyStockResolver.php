<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider\Stock;

use Magento\Framework\Exception\NoSuchEntityException;

class LegacyStockResolver implements StockResolverInterface
{
    /**
     * @var LegacyStockProvider
     */
    private $legacyStockProvider;

    /**
     * LegacyStockResolver constructor.
     * @param LegacyStockProvider $legacyStockProvider
     */
    public function __construct(
        LegacyStockProvider $legacyStockProvider
    ) {
        $this->legacyStockProvider = $legacyStockProvider;
    }

    /**
     * @return StockProviderInterface
     */
    public function resolve(): StockProviderInterface
    {
        return $this->legacyStockProvider;
    }
}
