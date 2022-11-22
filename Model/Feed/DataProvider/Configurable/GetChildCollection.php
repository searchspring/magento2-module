<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider\Configurable;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Product\Collection;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Product\CollectionFactory;

class GetChildCollection
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var Status
     */
    private $status;

    /**
     * GetChildCollection constructor.
     * @param CollectionFactory $collectionFactory
     * @param Status $status
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        Status $status
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->status = $status;
    }

    /**
     * @param Product[] $products
     * @param string[] $attributeCodes
     * @return Collection
     */
    public function execute(
        array $products,
        array $attributeCodes = []
    ) : Collection {
        $collection = $this->collectionFactory->create();
        foreach ($products as $product) {
            $collection->setProductFilter($product);
        }

        $defaultAttributes = [
            ProductInterface::STATUS,
            ProductInterface::SKU,
            ProductInterface::NAME,
            'special_price',
            'special_to_date',
            'special_from_date'
        ];

        $attributeCodes = array_unique(array_merge($attributeCodes, $defaultAttributes));
        $collection->addAttributeToSelect($attributeCodes);
        $collection->addAttributeToFilter(
            ProductInterface::STATUS,
            ['in' => $this->status->getVisibleStatusIds()]
        );

        $collection->addPriceData();

        return $collection;
    }
}
