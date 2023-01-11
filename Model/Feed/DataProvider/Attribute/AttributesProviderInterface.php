<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider\Attribute;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;

interface AttributesProviderInterface
{
    /**
     * @param FeedSpecificationInterface $feedSpecification
     * @return ProductAttributeInterface[]
     */
    public function getAttributes(FeedSpecificationInterface $feedSpecification) : array;

    /**
     * @param FeedSpecificationInterface $feedSpecification
     * @return array
     */
    public function getAttributeCodes(FeedSpecificationInterface $feedSpecification) : array;

    /**
     *
     */
    public function reset() : void;
}
