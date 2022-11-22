<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Catalog\Pricing\Price\RegularPrice;
use Magento\Framework\Serialize\Serializer\Json;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\Price\ProviderResolverInterface;
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
     * @var ProviderResolverInterface
     */
    private $priceProviderResolver;

    /**
     * PricesProvider constructor.
     * @param Json $json
     * @param ProviderResolverInterface $priceProviderResolver
     */
    public function __construct(
        Json $json,
        ProviderResolverInterface $priceProviderResolver
    ) {
        $this->json = $json;
        $this->priceProviderResolver = $priceProviderResolver;
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

            $priceProvider = $this->priceProviderResolver->resolve($productModel);
            $product = array_merge($product, $priceProvider->getPrices($productModel, $ignoredFields));

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
