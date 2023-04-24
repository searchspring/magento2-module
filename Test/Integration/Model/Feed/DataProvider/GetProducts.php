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

namespace SearchSpring\Feed\Test\Integration\Model\Feed\DataProvider;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\Collection\ProcessorPool;
use SearchSpring\Feed\Model\Feed\CollectionProviderInterface;
use Magento\TestFramework\Helper\Bootstrap;


class GetProducts
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var CollectionProviderInterface
     */
    private $collectionProvider;

    /**
     * GetProducts constructor.
     * @param CollectionProviderInterface $collectionProvider
     */
    public function __construct(
        CollectionProviderInterface $collectionProvider
    ) {
        $this->collectionProvider = $collectionProvider;
    }

    /**
     * @param FeedSpecificationInterface $specification
     * @return array
     */
    public function get(FeedSpecificationInterface $specification) : array
    {
        $collection = $this->collectionProvider->getCollection($specification);
        /** @var ProcessorPool $processorPool */
        $processorPool = Bootstrap::getObjectManager()->get('SearchSpringFeedGenerateFeedAfterLoadCollectionProcessorPool');
        foreach ($processorPool->getAll() as $processor) {
            $processor->processAfterLoad($collection, $specification);
        }

        $result = [];
        foreach ($collection as $item) {
            $result[] = [
                'entity_id' => $item->getEntityId(),
                'product_model' => $item
            ];
        }

        return $result;
    }
}
