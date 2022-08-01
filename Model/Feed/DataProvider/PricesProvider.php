<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Catalog\Pricing\Price\RegularPrice;
use Magento\Framework\Serialize\Serializer\Json;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\DataProviderInterface;

class PricesProvider implements DataProviderInterface
{
    const REGULAR_PRICE_KEY = 'regular_price';
    const FINAL_PRICE_KEY = 'final_price';
    const MAX_PRICE_KEY = 'max_price';
    /**
     * @var Json
     */
    private $json;

    /**
     * PricesProvider constructor.
     * @param Json $json
     */
    public function __construct(
        Json $json
    ) {
        $this->json = $json;
    }

    /**
     * @param array $products
     * @param FeedSpecificationInterface $feedSpecification
     * @return array
     */
    public function getData(array $products, FeedSpecificationInterface $feedSpecification): array
    {
        $ignoredFields = $feedSpecification->getIgnoreFields();
        foreach ($products as &$product) {
            /** @var Product $productModel */
            $productModel = $product['product_model'] ?? null;
            if (!$productModel) {
                continue;
            }

            if (!in_array(self::FINAL_PRICE_KEY, $ignoredFields)) {
                $product[self::FINAL_PRICE_KEY] = $productModel
                    ->getPriceInfo()
                    ->getPrice(FinalPrice::PRICE_CODE)
                    ->getMinimalPrice()
                    ->getValue();
            }

            if (!in_array(self::REGULAR_PRICE_KEY, $ignoredFields)) {
                $product[self::REGULAR_PRICE_KEY] = $productModel
                    ->getPriceInfo()
                    ->getPrice(RegularPrice::PRICE_CODE)
                    ->getValue();
            }

            if (!in_array(self::MAX_PRICE_KEY, $ignoredFields)) {
                $product[self::MAX_PRICE_KEY] = $productModel
                    ->getPriceInfo()
                    ->getPrice(self::FINAL_PRICE_KEY)
                    ->getMaximalPrice()
                    ->getValue();
            }

            if ($feedSpecification->getIncludeTierPricing() && !in_array('tier_pricing', $ignoredFields)) {
                $product['tier_pricing'] = $this->json->serialize($productModel->getTierPrice());
            }
        }

        return $products;
    }

    /**
     *
     */
    public function reset(): void
    {
        // do nothing
    }
}
