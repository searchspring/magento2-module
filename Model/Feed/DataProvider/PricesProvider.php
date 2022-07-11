<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider;

use Magento\Catalog\Model\Product;
use Magento\Framework\Serialize\Serializer\Json;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\DataProviderInterface;

class PricesProvider implements DataProviderInterface
{
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

            if (!in_array('final_price', $ignoredFields)) {
                $product['final_price'] = $productModel->getPriceInfo()->getPrice('final_price')->getMinimalPrice()->getValue();
            }

            if (!in_array('regular_price', $ignoredFields)) {
                $product['regular_price'] = $productModel->getPriceInfo()->getPrice('regular_price')->getValue();
            }

            if (!in_array('max_price', $ignoredFields)) {
                $product['max_price'] = $productModel->getPriceInfo()->getPrice('final_price')->getMaximalPrice()->getValue();
            }

            if ($feedSpecification->getIncludeTierPricing() && !in_array('tier_pricing', $ignoredFields)) {
                $product['tier_pricing'] = $this->json->serialize($productModel->getTierPrice());
            }
        }

        return $products;
    }
}
