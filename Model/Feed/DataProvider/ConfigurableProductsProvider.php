<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider;

use Exception;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute as ConfigurableAttribute;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\LocalizedException;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\Attribute\ChildAttributesProvider;
use SearchSpring\Feed\Model\Feed\DataProvider\Attribute\ValueProcessor;
use SearchSpring\Feed\Model\Feed\DataProvider\Configurable\GetAttributesCollection;
use SearchSpring\Feed\Model\Feed\DataProvider\Configurable\GetChildCollection;
use SearchSpring\Feed\Model\Feed\DataProvider\Product\GetChildProductsData;
use SearchSpring\Feed\Model\Feed\DataProviderInterface;

class ConfigurableProductsProvider implements DataProviderInterface
{
    /**
     * @var GetAttributesCollection
     */
    private $getAttributesCollection;
    /**
     * @var GetChildCollection
     */
    private $getChildCollection;
    /**
     * @var MetadataPool
     */
    private $metadataPool;
    /**
     * @var ChildAttributesProvider
     */
    private $childAttributesProvider;
    /**
     * @var GetChildProductsData
     */
    private $getChildProductsData;
    /**
     * @var ValueProcessor
     */
    private $valueProcessor;

    /**
     * ConfigurableProductsProvider constructor.
     * @param GetAttributesCollection $getAttributesCollection
     * @param GetChildCollection $getChildCollection
     * @param MetadataPool $metadataPool
     * @param ChildAttributesProvider $childAttributesProvider
     * @param GetChildProductsData $getChildProductsData
     * @param ValueProcessor $valueProcessor
     */
    public function __construct(
        GetAttributesCollection $getAttributesCollection,
        GetChildCollection $getChildCollection,
        MetadataPool $metadataPool,
        ChildAttributesProvider $childAttributesProvider,
        GetChildProductsData $getChildProductsData,
        ValueProcessor $valueProcessor
    ) {
        $this->getAttributesCollection = $getAttributesCollection;
        $this->getChildCollection = $getChildCollection;
        $this->metadataPool = $metadataPool;
        $this->childAttributesProvider = $childAttributesProvider;
        $this->getChildProductsData = $getChildProductsData;
        $this->valueProcessor = $valueProcessor;
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
                $this->getChildProductsData->getProductData(
                    $product,
                    $childProducts[$id],
                    $configurableAttributes[$id],
                    $feedSpecification
                )
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

        $specificationAttributes = $this->childAttributesProvider->getAttributes($feedSpecification);
        foreach($specificationAttributes as $attribute) {
            if(!isset($result[$attribute->getAttributeId()])) {
                $result[$attribute->getAttributeId()] = $attribute;
            }
        }

        return $result;
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

        $specificationAttributes = $this->childAttributesProvider->getAttributes($feedSpecification);
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
        $this->childAttributesProvider->reset();
        $this->valueProcessor->reset();
    }
}
