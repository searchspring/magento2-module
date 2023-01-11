<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider\Price;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Catalog\Pricing\Price\RegularPrice;
use SearchSpring\Feed\Model\Feed\DataProvider\PricesProvider;

class BasePriceProvider implements PriceProviderInterface
{
    /**
     * @var Type
     */
    private $type;

    /**
     * BasePriceProvider constructor.
     * @param Type $type
     */
    public function __construct(
        Type $type
    ) {
        $this->type = $type;
    }

    /**
     * @param ProductInterface $product
     * @param array $ignoredFields
     * @return array
     */
    public function getPrices(ProductInterface $product, array $ignoredFields): array
    {
        $result = [];
        if (!in_array(PricesProvider::FINAL_PRICE_KEY, $ignoredFields)) {
            $result[PricesProvider::FINAL_PRICE_KEY] = $product
                ->getPriceInfo()
                ->getPrice(FinalPrice::PRICE_CODE)
                ->getMinimalPrice()
                ->getValue();
        }

        if (!in_array(PricesProvider::REGULAR_PRICE_KEY, $ignoredFields)) {
            $result[PricesProvider::REGULAR_PRICE_KEY] = $product
                ->getPriceInfo()
                ->getPrice(RegularPrice::PRICE_CODE)
                ->getValue();
        }

        if (!in_array(PricesProvider::MAX_PRICE_KEY, $ignoredFields)) {
            $result[PricesProvider::MAX_PRICE_KEY] = $product
                ->getPriceInfo()
                ->getPrice(FinalPrice::PRICE_CODE)
                ->getMaximalPrice()
                ->getValue();
        }

        return $result;
    }
}
