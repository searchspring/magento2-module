<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider\Attribute;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;

class FeedAttributesProvider implements AttributesProviderInterface
{
    /**
     * @var ProductAttributeRepositoryInterface
     */
    private $productAttributeRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var ProductAttributeInterface[]
     */
    private $attributes = null;

    /**
     * FeedAttributesProvider constructor.
     * @param ProductAttributeRepositoryInterface $productAttributeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        ProductAttributeRepositoryInterface $productAttributeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->productAttributeRepository = $productAttributeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param FeedSpecificationInterface $feedSpecification
     * @return ProductAttributeInterface[]
     */
    public function getAttributes(FeedSpecificationInterface $feedSpecification): array
    {
        if (is_null($this->attributes)) {
            $restrictedAttributes = $feedSpecification->getIgnoreFields();
            if (!empty($restrictedAttributes)) {
                $this->searchCriteriaBuilder->addFilter(
                    ProductAttributeInterface::ATTRIBUTE_CODE,
                    $restrictedAttributes,
                    'nin'
                );
            }

            $searchCriteria = $this->searchCriteriaBuilder
                ->create();
            $this->attributes = $this->productAttributeRepository->getList($searchCriteria)->getItems();
        }
        return $this->attributes;
    }

    /**
     * @param FeedSpecificationInterface $feedSpecification
     * @return array
     */
    public function getAttributeCodes(FeedSpecificationInterface $feedSpecification): array
    {
        $attributes = $this->getAttributes($feedSpecification);
        $codes = array_map(function ($attribute) {
            return $attribute->getAttributeCode();
        }, $attributes);

        return $codes;
    }

    /**
     *
     */
    public function reset(): void
    {
        $this->attributes = null;
    }
}
