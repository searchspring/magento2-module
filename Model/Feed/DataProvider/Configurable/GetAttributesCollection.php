<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider\Configurable;

use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Attribute\Collection;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;

class GetAttributesCollection
{
    /**
     * @var JoinProcessorInterface
     */
    private $joinProcessor;
    /**
     * @var AttributeCollectionFactory
     */
    private $attributeCollectionFactory;

    /**
     * GetAttributesCollection constructor.
     * @param JoinProcessorInterface $joinProcessor
     * @param AttributeCollectionFactory $attributeCollectionFactory
     */
    public function __construct(
        JoinProcessorInterface $joinProcessor,
        AttributeCollectionFactory $attributeCollectionFactory
    ) {
        $this->joinProcessor = $joinProcessor;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
    }

    /**
     * @param Product[] $products
     * @return Collection
     */
    public function execute(array $products) : Collection
    {
        $attributesCollection = $this->attributeCollectionFactory->create();
        foreach ($products as $product) {
            $attributesCollection->setProductFilter($product);
        }

        $this->joinProcessor->process($attributesCollection);
        $attributesCollection->orderByPosition();

        return $attributesCollection;
    }
}
