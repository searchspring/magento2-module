<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider;

use Exception;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\DataProviderInterface;
use SearchSpring\Feed\Model\Feed\SystemFieldsList;

class AttributesProvider implements DataProviderInterface
{
    /**
     * @var ProductAttributeInterface[]
     */
    private $attributes = [];
    /**
     * @var SystemFieldsList
     */
    private $systemFieldsList;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var ProductAttributeRepositoryInterface
     */
    private $productAttributeRepository;

    /**
     * AttributesProvider constructor.
     * @param SystemFieldsList $systemFieldsList
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ProductAttributeRepositoryInterface $productAttributeRepository
     */
    public function __construct(
        SystemFieldsList $systemFieldsList,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductAttributeRepositoryInterface $productAttributeRepository
    ) {
        $this->systemFieldsList = $systemFieldsList;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productAttributeRepository = $productAttributeRepository;
    }

    /**
     * @param array $products
     * @param FeedSpecificationInterface $feedSpecification
     * @return array
     * @throws Exception
     */
    public function getData(array $products, FeedSpecificationInterface $feedSpecification): array
    {
        $this->loadAttributesFromProducts($products);
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
            if ($attribute->usesSource()) {
                $value = $attribute->getSource()->getOptionText($fieldValue);
            } else {
                $value = $fieldValue;
            }

            if (is_object($value)) {
                if ($value instanceof Phrase) {
                    $value = $value->getText();
                } else {
                    throw new Exception("Unknown value object type " . get_class($value));
                }
            }

            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * @param array $products
     */
    private function loadAttributesFromProducts(array $products) : void
    {
        $loadedAttributeKeys = array_keys($this->attributes);
        $productsAttributeKeys = [];
        foreach ($products as $product) {
            /** @var Product $productModel */
            $productModel = $product['product_model'] ?? null;
            if (!$productModel) {
                continue;
            }

            $productsAttributeKeys = array_merge($productsAttributeKeys, array_keys($productModel->getData()));
        }

        $productsAttributeKeys = array_unique($productsAttributeKeys);
        $notLoadedAttributes = array_diff($productsAttributeKeys, $loadedAttributeKeys);
        $systemAttributes = $this->systemFieldsList->get();
        foreach ($notLoadedAttributes as $key => $notLoadedAttribute) {
            if (in_array($notLoadedAttribute, $systemAttributes)) {
                unset($notLoadedAttribute[$key]);
            }
        }

        if (empty($notLoadedAttributes)) {
            return;
        }

        $this->loadAttributes($notLoadedAttributes);
    }

    /**
     * @param array $keys
     */
    private function loadAttributes(array $keys) : void
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(
            ProductAttributeInterface::ATTRIBUTE_CODE,
            $keys,
            'in'
        )->create();

        $attributes = $this->productAttributeRepository->getList($searchCriteria)->getItems();
        foreach ($attributes as $attribute) {
            $this->attributes[$attribute->getAttributeCode()] = $attribute;
        }
    }

    /**
     *
     */
    public function reset(): void
    {
        $this->attributes = [];
    }
}
