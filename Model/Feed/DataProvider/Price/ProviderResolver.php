<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider\Price;

use Magento\Catalog\Api\Data\ProductInterface;

class ProviderResolver implements ProviderResolverInterface
{
    /**
     * @var PriceProviderInterface
     */
    private $basePriceProvider;
    /**
     * @var PriceProviderInterface[]
     */
    private $priceProviders;

    /**
     * ProviderResolver constructor.
     * @param PriceProviderInterface $basePriceProvider
     * @param array $priceProviders
     */
    public function __construct(
        PriceProviderInterface $basePriceProvider,
        array $priceProviders = []
    ) {
        $this->basePriceProvider = $basePriceProvider;
        $this->priceProviders = $priceProviders;
    }

    /**
     * @param ProductInterface $product
     * @return PriceProviderInterface
     */
    public function resolve(ProductInterface $product): PriceProviderInterface
    {
        $type = $product->getTypeId();
        return $this->priceProviders[$type] ?? $this->basePriceProvider;
    }
}
