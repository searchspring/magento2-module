<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider\Attribute;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Eav\Model\Config;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;

class ChildAttributesProvider
{
    /**
     * @var Attribute[]|null
     */
    private $specificationAttributes;
    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * ChildAttributesProvider constructor.
     * @param Config $eavConfig
     */
    public function __construct(
        Config $eavConfig
    ) {
        $this->eavConfig = $eavConfig;
    }

    /**
     * @param FeedSpecificationInterface $feedSpecification
     * @return Attribute[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAttributes(FeedSpecificationInterface $feedSpecification) : array
    {
        if (is_null($this->specificationAttributes)) {
            $childFields = $feedSpecification->getChildFields();
            $specificationAttributes = [];
            foreach ($childFields as $attribute) {
                $productAttribute = $this->eavConfig->getAttribute("catalog_product", $attribute);
                if ($productAttribute && !isset($result[$productAttribute->getAttributeId()])) {
                    $specificationAttributes[$productAttribute->getAttributeId()] = $productAttribute;
                }
            }

            $this->specificationAttributes = $specificationAttributes;
        }

        return $this->specificationAttributes;
    }

    /**
     *
     */
    public function reset() : void
    {
        $this->specificationAttributes = null;
    }
}
