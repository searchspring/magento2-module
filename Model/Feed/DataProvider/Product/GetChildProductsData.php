<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider\Product;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\Exception\LocalizedException;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\Attribute\ValueProcessor;

class GetChildProductsData
{
    /**
     * @var ValueProcessor
     */
    private $valueProcessor;

    /**
     * GetChildProductsData constructor.
     * @param ValueProcessor $valueProcessor
     */
    public function __construct(
        ValueProcessor $valueProcessor
    ) {
        $this->valueProcessor = $valueProcessor;
    }

    /**
     * @param array $productData
     * @param Product[] $childProducts
     * @param Attribute[] $attributes
     * @param FeedSpecificationInterface $feedSpecification
     * @return array
     * @throws LocalizedException
     */
    public function getProductData(
        array $productData,
        array $childProducts,
        array $attributes,
        FeedSpecificationInterface $feedSpecification
    ) : array {
        $result = [];
        $ignoredFields = $feedSpecification->getIgnoreFields();
        foreach($childProducts as $child) {
            foreach($attributes as $childAttribute) {
                $code = $childAttribute->getAttributeCode();
                if (in_array($code, $ignoredFields)) {
                    continue;
                }

                $value =$this->valueProcessor->getValue($childAttribute, $child->getData($code));
                if ($value != '' && !empty($value)) {
                    $result[$code][] = $value;
                }
            }

            if (!in_array('child_sku', $ignoredFields) && $child->getSku() != '') {
                $result['child_sku'][] = $child->getSku();
            }

            if (!in_array('child_name', $ignoredFields) && $child->getName() != '') {
                $result['child_name'][] = $child->getName();
            }

            if($feedSpecification->getIncludeChildPrices() && !in_array('child_final_price', $ignoredFields)) {
                $price = $child->getPriceInfo()->getPrice(FinalPrice::PRICE_CODE)->getMinimalPrice()->getValue();
                $result['child_final_price'][] = $price;
            }
        }

        foreach ($result as $key => &$value) {
            if (isset($productData[$key])) {
                $productDataValue = is_array($productData[$key]) ? $productData[$key] : [$productData[$key]];
                $value = array_merge($productDataValue, $value);
            }
        }

        return $result;
    }
}
