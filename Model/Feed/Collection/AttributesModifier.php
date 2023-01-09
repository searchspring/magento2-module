<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\Collection;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\Attribute\AttributesProviderInterface;

class AttributesModifier implements ModifierInterface
{
    /**
     * @var array
     */
    private $appliedAttributes;
    /**
     * @var AttributesProviderInterface
     */
    private $attributesProvider;

    /**
     * AttributesModifier constructor.
     * @param AttributesProviderInterface $attributesProvider
     * @param array $appliedAttributes
     */
    public function __construct(
        AttributesProviderInterface $attributesProvider,
        array $appliedAttributes = []
    ) {
        $this->appliedAttributes = $appliedAttributes;
        $this->attributesProvider = $attributesProvider;
    }

    /**
     * @param Collection $collection
     * @param FeedSpecificationInterface $feedSpecification
     * @return Collection
     */
    public function modify(Collection $collection, FeedSpecificationInterface $feedSpecification): Collection
    {
        $productAttributes = $this->attributesProvider->getAttributes($feedSpecification);
        $codes = [];
        foreach ($productAttributes as $attribute) {
            $code = $attribute->getAttributeCode();
            if (!in_array($code, $this->appliedAttributes)) {
                $codes[] = $code;
            }
        }

        $collection->addAttributeToSelect($codes);

        return $collection;
    }
}
