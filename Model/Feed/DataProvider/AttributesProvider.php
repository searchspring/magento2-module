<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider;

use Exception;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\Exception\LocalizedException;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\Attribute\AttributesProviderInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\Attribute\ValueProcessor;
use SearchSpring\Feed\Model\Feed\DataProviderInterface;
use SearchSpring\Feed\Model\Feed\SystemFieldsList;

class AttributesProvider implements DataProviderInterface
{
    /**
     * @var ProductAttributeInterface[]|null
     */
    private $attributes = null;
    /**
     * @var SystemFieldsList
     */
    private $systemFieldsList;
    /**
     * @var ValueProcessor
     */
    private $valueProcessor;
    /**
     * @var AttributesProviderInterface
     */
    private $attributesProvider;

    /**
     * AttributesProvider constructor.
     * @param SystemFieldsList $systemFieldsList
     * @param ValueProcessor $valueProcessor
     * @param AttributesProviderInterface $attributesProvider
     */
    public function __construct(
        SystemFieldsList $systemFieldsList,
        ValueProcessor $valueProcessor,
        AttributesProviderInterface $attributesProvider
    ) {
        $this->systemFieldsList = $systemFieldsList;
        $this->valueProcessor = $valueProcessor;
        $this->attributesProvider = $attributesProvider;
    }

    /**
     * @param array $products
     * @param FeedSpecificationInterface $feedSpecification
     * @return array
     * @throws Exception
     */
    public function getData(array $products, FeedSpecificationInterface $feedSpecification): array
    {
        $this->loadAttributes($feedSpecification);
        foreach ($products as &$product) {
            $productModel = $product['product_model'] ?? null;
            if (!$productModel) {
                continue;
            }
            $product = array_merge($product, $this->getProductData($productModel));
        }

        return $products;
    }

    /**
     * @param Product $product
     * @return array
     * @throws LocalizedException
     * @throws Exception
     */
    private function getProductData(Product $product) : array
    {
        $productData = $product->getData();
        $result = [];
        foreach ($productData as $key => $fieldValue) {
            if (!isset($this->attributes[$key])) {
                continue;
            }
            /** @var Attribute $attribute */
            $attribute = $this->attributes[$key];
            $result[$key] = $this->valueProcessor->getValue($attribute, $fieldValue);
        }

        return $result;
    }

    /**
     * @param FeedSpecificationInterface $feedSpecification
     */
    private function loadAttributes(FeedSpecificationInterface $feedSpecification) : void
    {
        if (is_null($this->attributes)) {
            $attributes = $this->attributesProvider->getAttributes($feedSpecification);
            $systemAttributes = $this->systemFieldsList->get();
            foreach ($attributes as $attribute) {
                if (!in_array($attribute->getAttributeCode(), $systemAttributes)) {
                    $this->attributes[$attribute->getAttributeCode()] = $attribute;
                }
            }
        }
    }

    /**
     *
     */
    public function reset(): void
    {
        $this->attributes = [];
        $this->valueProcessor->reset();
        $this->attributesProvider->reset();
    }

    /**
     *
     */
    public function resetAfterFetchItems(): void
    {
        // do nothing
    }
}
