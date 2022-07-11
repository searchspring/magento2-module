<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider;

use Magento\Catalog\Model\Product;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\DataProviderInterface;

class SaleableProvider implements DataProviderInterface
{

    /**
     * @param array $products
     * @param FeedSpecificationInterface $feedSpecification
     * @return array
     */
    public function getData(array $products, FeedSpecificationInterface $feedSpecification): array
    {
        foreach ($products as &$product) {
            /** @var Product $productModel */
            $productModel = $product['product_model'] ?? null;
            if (!$productModel) {
                continue;
            }

            $product['saleable'] = $productModel->isSaleable();
        }

        return $products;
    }
}
