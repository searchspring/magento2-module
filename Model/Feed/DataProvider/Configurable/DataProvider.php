<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider\Configurable;

use Exception;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute as ConfigurableAttribute;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\LocalizedException;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\Attribute\AttributesProviderInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\Attribute\ChildAttributesProvider;
use SearchSpring\Feed\Model\Feed\DataProvider\Attribute\ValueProcessor;
use SearchSpring\Feed\Model\Feed\DataProvider\Configurable\GetAttributesCollection;
use SearchSpring\Feed\Model\Feed\DataProvider\Configurable\GetChildCollection;
use SearchSpring\Feed\Model\Feed\DataProvider\Product\ChildStorage;
use SearchSpring\Feed\Model\Feed\DataProvider\Product\GetChildProductsData;

class DataProvider
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
     * @var ChildStorage
     */
    private $childStorage;
    /**
     * @var AttributesProviderInterface[]
     */
    private $attributesProvider;

    /**
     * @var array|null
     */
    private $configurableAttributes = null;

    /**
     * @var ProductAttributeInterface[]
     */
    private $attributes = null;

    /**
     * ConfigurableProductsProvider constructor.
     * @param GetAttributesCollection $getAttributesCollection
     * @param GetChildCollection $getChildCollection
     * @param MetadataPool $metadataPool
     * @param ChildAttributesProvider $childAttributesProvider
     * @param GetChildProductsData $getChildProductsData
     * @param ValueProcessor $valueProcessor
     * @param ChildStorage $childStorage
     * @param array $attributesProvider
     */
    public function __construct(
        GetAttributesCollection $getAttributesCollection,
        GetChildCollection $getChildCollection,
        MetadataPool $metadataPool,
        ChildAttributesProvider $childAttributesProvider,
        GetChildProductsData $getChildProductsData,
        ValueProcessor $valueProcessor,
        ChildStorage $childStorage,
        array $attributesProvider = []
    ) {
        $this->getAttributesCollection = $getAttributesCollection;
        $this->getChildCollection = $getChildCollection;
        $this->metadataPool = $metadataPool;
        $this->childAttributesProvider = $childAttributesProvider;
        $this->getChildProductsData = $getChildProductsData;
        $this->valueProcessor = $valueProcessor;
        $this->childStorage = $childStorage;
        $this->attributesProvider = $attributesProvider;
    }

    /**
     * @param int $productId
     * @return array|null
     */
    public function getById(int $productId): ?array
    {
        return $this->childStorage->getById($productId);
    }

    /**
     * @param array $products
     * @param FeedSpecificationInterface $feedSpecification
     * @return array
     * @throws LocalizedException
     */
    public function getAllChildProducts(array $products, FeedSpecificationInterface $feedSpecification): array
    {
        $configurableProducts = $this->getConfigurableProducts($products);
        if (empty($configurableProducts)) {
            return $products;
        }

        if (!array_diff_key($this->childStorage->get(), $configurableProducts)
            && count($this->childStorage->get())
        ) {
            return $this->childStorage->get();
        }

        $attributesCollection = $this->getAttributesCollection->execute($configurableProducts);
        $childAttributes = $this->getChildAttributes($attributesCollection->getItems(), $feedSpecification);
        $childAttributeCodes = array_map(function ($attribute) {
            return $attribute->getAttributeCode();
        }, $childAttributes);
        $childProductsCollection = $this->getChildCollection->execute($configurableProducts, $childAttributeCodes);
        $childProducts = $this->processChildProducts($childProductsCollection->getItems());
        $this->childStorage->set($childProducts);

        return $childProducts;
    }

    /**
     * @param array $products
     * @return array
     * @throws Exception
     */
    public function getConfigurableProducts(array $products) : array
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
     * @param array $configurableProducts
     * @param FeedSpecificationInterface $feedSpecification
     * @return array
     * @throws LocalizedException
     */
    public function getConfigurableAttributes(
        array $configurableProducts,
        FeedSpecificationInterface $feedSpecification
    ): array {
        if (is_null($this->configurableAttributes)) {
            $attributes = $this->getAttributes($configurableProducts, $feedSpecification);
            $this->configurableAttributes = $this->processAttributes($attributes, $feedSpecification);
        }

        return $this->configurableAttributes;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getLinkField() : string
    {
        return $this->metadataPool->getMetadata(ProductInterface::class)->getLinkField();
    }

    /**
     * @return void
     */
    public function reset(): void
    {
        $this->childAttributesProvider->reset();
        $this->valueProcessor->reset();
        $this->resetAfterFetchItems();
    }

    /**
     * @return void
     */
    public function resetAfterFetchItems(): void
    {
        $this->childStorage->reset();
        $this->attributes = null;
        $this->configurableAttributes = null;
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
        foreach ($specificationAttributes as $attribute) {
            if (!isset($result[$attribute->getAttributeId()])) {
                $result[$attribute->getAttributeId()] = $attribute;
            }
        }

        return $result;
    }

    /**
     * @param array $configurableProducts
     * @param FeedSpecificationInterface $feedSpecification
     * @return ProductAttributeInterface[]
     */
    private function getAttributes(array $configurableProducts, FeedSpecificationInterface $feedSpecification) : array
    {
        if (is_null($this->attributes)) {
            $attributesCollection = $this->getAttributesCollection->execute($configurableProducts);

            $attributes = [];
            foreach ($attributesCollection as $attribute) {
                $attributes[$attribute->getAttributeCode()] = $attribute;
            }

            foreach ($this->attributesProvider as $attributesProvider) {
                $newAttributes = $attributesProvider->getAttributes($feedSpecification);
                foreach ($newAttributes as $attribute) {
                    if (!isset($attributes[$attribute->getAttributeCode()])) {
                        $attributes[$attribute->getAttributeCode()] = $attribute;
                    }
                }
            }

            $this->attributes = $attributes;
        }

        return $this->attributes;
    }
}
