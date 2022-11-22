<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider\Price;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Catalog\Pricing\Price\RegularPrice;
use Magento\ConfigurableProduct\Pricing\Price\ConfigurableOptionsProviderInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\Product\ChildStorage;
use SearchSpring\Feed\Model\Feed\DataProvider\PricesProvider;

class ConfigurablePriceProvider implements PriceProviderInterface
{
    /**
     * @var ChildStorage
     */
    private $childStorage;
    /**
     * @var ConfigurableOptionsProviderInterface
     */
    private $configurableOptionsProvider;

    /**
     * ConfigurablePriceProvider constructor.
     * @param ChildStorage $childStorage
     * @param ConfigurableOptionsProviderInterface $configurableOptionsProvider
     */
    public function __construct(
        ChildStorage $childStorage,
        ConfigurableOptionsProviderInterface $configurableOptionsProvider
    ) {
        $this->childStorage = $childStorage;
        $this->configurableOptionsProvider = $configurableOptionsProvider;
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
            $maximumAmount = $product->hasMaxPrice() ? (float) $product->getMaxPrice() : null;
            if (is_null($maximumAmount)) {
                $childProducts = $this->childStorage->getById((int)$product->getId())
                    ?? $this->configurableOptionsProvider->getProducts($product);
                foreach ($childProducts as $variant) {
                    $variantAmount = $variant->getPriceInfo()->getPrice(FinalPrice::PRICE_CODE)->getAmount();
                    if (!$maximumAmount || ($variantAmount->getValue() > $maximumAmount)) {
                        $maximumAmount = $variantAmount->getValue();
                    }
                }
            }

            $result[PricesProvider::MAX_PRICE_KEY] = $maximumAmount;
        }

        return $result;
    }
}
