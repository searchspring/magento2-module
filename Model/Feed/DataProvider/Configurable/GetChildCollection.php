<?php
/**
 * Copyright (C) 2023 Searchspring <https://searchspring.com>
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

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
