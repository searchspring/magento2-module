<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider;

use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\DataProviderInterface;

class AttributesProvider implements DataProviderInterface
{

    /**
     * @param array $products
     * @param FeedSpecificationInterface $feedSpecification
     * @return array
     */
    public function getData(array $products, FeedSpecificationInterface $feedSpecification): array
    {
        foreach ($products as &$product) {
            $product = array_merge($product, $product['product_model']->getData());
        }

        return $products;
    }
}
