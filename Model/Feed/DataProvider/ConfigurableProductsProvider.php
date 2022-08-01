<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider;

use Exception;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute as ConfigurableAttribute;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Product\CollectionFactory;
use Magento\Eav\Model\Config;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\LocalizedException;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\Attribute\ValueProcessor;
use SearchSpring\Feed\Model\Feed\DataProvider\Configurable\GetAttributesCollection;
use SearchSpring\Feed\Model\Feed\DataProvider\Configurable\GetChildCollection;
use SearchSpring\Feed\Model\Feed\DataProviderInterface;

class ConfigurableProductsProvider implements DataProviderInterface
{
    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;
    /**
     * @var GetAttributesCollection
     */
    private $getAttributesCollection;
    /**
     * @var GetChildCollection
     */
    private $getChildCollection;
    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * @var Attribute[]|null
     */
    private $specificationAttributes = null;
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * ConfigurableProductsProvider constructor.
     * @param CollectionFactory $productCollectionFactory
     * @param GetAttributesCollection $getAttributesCollection
     * @param GetChildCollection $getChildCollection
     * @param Config $eavConfig
     * @param MetadataPool $metadataPool
     */
    public function __construct(
        CollectionFactory $productCollectionFactory,
        GetAttributesCollection $getAttributesCollection,
        GetChildCollection $getChildCollection,
        Config $eavConfig,
        MetadataPool $metadataPool
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->getAttributesCollection = $getAttributesCollection;
        $this->getChildCollection = $getChildCollection;
        $this->eavConfig = $eavConfig;
        $this->metadataPool = $metadataPool;
    }

    /**
     * @param array $products
     * @param FeedSpecificationInterface $feedSpecification
     * @return array
     * @throws LocalizedException
     */
    public function getData(array $products, FeedSpecificationInterface $feedSpecification): array
    {
        $configurableProducts = $this->getConfigurableProducts($products);
        if (empty($configurableProducts)) {
            return $products;
        }

        $attributesCollection = $this->getAttributesCollection->execute($configurableProducts);
        $configurableAttributes = $this->processAttributes($attributesCollection->getItems(), $feedSpecification);
        $childAttributes = $this->getChildAttributes($attributesCollection->getItems(), $feedSpecification);
        $childAttributeCodes = array_map(function ($attribute) {
            return $attribute->getAttributeCode();
        }, $childAttributes);
        $childProductsCollection = $this->getChildCollection->execute($configurableProducts, $childAttributeCodes);
        $childProducts = $this->processChildProducts($childProductsCollection->getItems());
        foreach ($products as &$product) {
            /** @var Product $productModel */
            $productModel = $product['product_model'] ?? null;
            if (!$productModel) {
                continue;
            }

            $id = $productModel->getData($this->getLinkField());
            if (!isset($childProducts[$id]) || !isset($configurableAttributes[$id])) {
                continue;
            }

            $product = array_merge(
                $product,
                $this->getProductData($product, $childProducts[$id], $configurableAttributes[$id], $feedSpecification)
            );
        }

        return $products;
    }

    /**
     * @param Product[] $childProducts
     * @return array
     */
    private function processChildProducts(array $childProducts) : array
    {
        $result = [];
        foreach ($childProducts as $product) {
            if ($product->getParentId()) {
                $result[$product->getParentId()][] = $product;
            }
        }

        return $result;
    }

    /**
     * @param array $productData
     * @param Product[] $childProducts
     * @param Attribute[] $attributes
     * @param FeedSpecificationInterface $feedSpecification
     * @return array
     * @throws LocalizedException
     */
    private function getProductData(
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

                $value = ValueProcessor::getValue($childAttribute, $child->getData($code));
                if ($value != '' && !empty($value)) {
                    $result[$code][] = $value;
                }
            }

            if (!in_array('child_sku', $ignoredFields) && $child->getSku() != '') {
                $result['child_sku'][] = $child->getSku();
            }

            if (!in_array('child_name', $ignoredFields) && $child->getName() != '') {
                $result['child_name'][] = $child->getSku();
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

    /**
     * @param ConfigurableAttribute[] $attributes
     * @param FeedSpecificationInterface $feedSpecification
     * @return Attribute[]
     * @throws LocalizedException
     */
    private function getChildAttributes(array $attributes, FeedSpecificationInterface $feedSpecification) : array
    {
        $result = [];
        foreach ($attributes as $attribute) {
            if (!isset($result[$attribute->getAttributeId()]) && $attribute->getProductAttribute()) {
                $result[$attribute->getAttributeId()] = $attribute->getProductAttribute();
            }
        }

        $specificationAttributes = $this->getAttributesFromSpecification($feedSpecification);
        foreach($specificationAttributes as $attribute) {
            if(!isset($result[$attribute->getAttributeId()])) {
                $result[$attribute->getAttributeId()] = $attribute;
            }
        }

        return $result;
    }

    /**
     * @param FeedSpecificationInterface $feedSpecification
     * @return Attribute[]
     * @throws LocalizedException
     */
    private function getAttributesFromSpecification(FeedSpecificationInterface $feedSpecification) : array
    {
        if (is_null($this->specificationAttributes)) {
            $childFields = $feedSpecification->getChildFields();
            foreach ($childFields as $attribute) {
                $productAttribute = $this->eavConfig->getAttribute("catalog_product", $attribute);
                if ($productAttribute && !isset($result[$productAttribute->getAttributeId()])) {
                    $this->specificationAttributes[$productAttribute->getAttributeId()] = $productAttribute;
                }
            }
        }

        return $this->specificationAttributes;
    }

    /**
     * @param array $products
     * @return array
     * @throws Exception
     */
    private function getConfigurableProducts(array $products) : array
    {
        $configurableProducts = [];
        foreach ($products as $product) {
            /** @var Product $productModel */
            $productModel = $product['product_model'] ?? null;
            if (!$productModel) {
                continue;
            }

            if (Configurable::TYPE_CODE === $productModel->getTypeId()) {
                $id = $productModel->getData($this->getLinkField());
                if ($id) {
                    $configurableProducts[$id] = $productModel;
                }
            }
        }

        return $configurableProducts;
    }

    /**
     * @return string
     * @throws Exception
     */
    private function getLinkField() : string
    {
        return $this->metadataPool->getMetadata(ProductInterface::class)->getLinkField();
    }

    /**
     * @param ConfigurableAttribute[] $attributes
     * @param FeedSpecificationInterface $feedSpecification
     * @return array
     * @throws LocalizedException
     */
    private function processAttributes(array $attributes, FeedSpecificationInterface $feedSpecification) : array
    {
        $result = [];
        foreach ($attributes as $attribute) {
            $productAttribute = $attribute->getProductAttribute();
            if ($productAttribute) {
                $result[$attribute->getProductId()][$productAttribute->getAttributeId()] = $productAttribute;
            }
        }

        $specificationAttributes = $this->getAttributesFromSpecification($feedSpecification);
        foreach ($result as $productId => &$productAttributes) {
            foreach ($specificationAttributes as $specificationAttribute) {
                if (!isset($productAttributes[$specificationAttribute->getAttributeId()])) {
                    $productAttributes[$specificationAttribute->getAttributeId()] = $specificationAttribute;
                }
            }
        }

        return $result;
    }

    /**
     *
     */
    public function reset(): void
    {
        $this->specificationAttributes = null;
    }
}
