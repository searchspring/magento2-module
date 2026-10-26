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

namespace SearchSpring\Feed\Model\Feed;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\Collection\ModifierInterface;

class CollectionProvider implements CollectionProviderInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var ModifierInterface[]
     */
    private $modifiers;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * CollectionProvider constructor.
     * @param CollectionFactory $collectionFactory
     * @param array $modifiers
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        array $modifiers = [],
        ?LoggerInterface $logger = null
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->modifiers = $modifiers;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param FeedSpecificationInterface $specification
     * @return Collection
     * @throws \Exception
     */
    public function getCollection(FeedSpecificationInterface $specification): Collection
    {
        $collection = $this->collectionFactory->create();
        $modifiers = $this->sort($this->modifiers);
        foreach ($modifiers as $key => $modifierData) {
            /** @var ModifierInterface $modifier */
            $modifier = $modifierData['objectInstance'] ?? null;
            if (!$modifier) {
                throw new \Exception((string) __('No objectInstance for modifier %1', $key));
            }
            $startTime = microtime(true);
            $collection = $modifier->modify($collection, $specification);
            $this->logModifier((string) $key, $modifier, $collection, $specification, $startTime);
        }

        return $collection;
    }

    /**
     * Logs the collection query after each modifier, attributes selected by addAttributeToSelect()
     * are joined on load, so they are not part of the query yet.
     *
     * @param string $key
     * @param ModifierInterface $modifier
     * @param Collection $collection
     * @param FeedSpecificationInterface $specification
     * @param float $startTime
     * @return void
     */
    private function logModifier(
        string $key,
        ModifierInterface $modifier,
        Collection $collection,
        FeedSpecificationInterface $specification,
        float $startTime
    ): void {
        try {
            $this->logger->info('Collection modifier applied', [
                'method' => __METHOD__,
                'store' => $specification->getStoreCode(),
                'modifier' => $key,
                'class' => get_class($modifier),
                'seconds' => round(microtime(true) - $startTime, 4),
                'query' => $collection->getSelect()->__toString(),
            ]);
        } catch (\Throwable $exception) {
            // logging must never break the feed generation
        }
    }

    /**
     * @param array $data
     * @return array
     */
    private function sort(array $data)
    {
        // uasort keeps the modifier keys for logging
        uasort($data, function (array $a, array $b) {
            return $this->getSortOrder($a) <=> $this->getSortOrder($b);
        });

        return $data;
    }

    /**
     * @param array $variable
     * @return int
     */
    private function getSortOrder(array $variable)
    {
        return !empty($variable['sortOrder']) ? (int) $variable['sortOrder'] : 0;
    }
}
