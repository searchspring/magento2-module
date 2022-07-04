<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\Collection;

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Api\SearchCriteriaBuilder;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;

class AttributesModifier implements ModifierInterface
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
     * @var array
     */
    private $appliedAttributes;

    /**
     * AttributesModifier constructor.
     * @param ProductAttributeRepositoryInterface $productAttributeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $appliedAttributes
     */
    public function __construct(
        ProductAttributeRepositoryInterface $productAttributeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        array $appliedAttributes = []
    ) {
        $this->productAttributeRepository = $productAttributeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->appliedAttributes = $appliedAttributes;
    }

    /**
     * @param Collection $collection
     * @param FeedSpecificationInterface $feedSpecification
     * @return Collection
     */
    public function modify(Collection $collection, FeedSpecificationInterface $feedSpecification): Collection
    {
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $productAttributes = $this->productAttributeRepository->getList($searchCriteria)->getItems();
        $codes = [];
        $restrictedAttributes = $feedSpecification->getIgnoreFields();
        foreach ($productAttributes as $attribute) {
            $code = $attribute->getAttributeCode();
            if (in_array($code, $this->appliedAttributes) || in_array($code, $restrictedAttributes)) {
                continue;
            }

            $codes[] = $attribute->getAttributeCode();
        }

        $collection->addAttributeToSelect($codes);

        return $collection;
    }
}
