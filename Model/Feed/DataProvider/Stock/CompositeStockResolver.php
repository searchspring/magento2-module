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

namespace SearchSpring\Feed\Model\Feed\DataProvider\Stock;

use Magento\Framework\Exception\NoSuchEntityException;

class CompositeStockResolver implements StockResolverInterface
{
    /**
     * @var array
     */
    private $resolvers;

    /**
     * CompositeStockResolver constructor.
     * @param array $resolvers
     */
    public function __construct(
        array $resolvers = []
    ) {
        $this->resolvers = $resolvers;
    }

    /**
     * @return StockProviderInterface
     * @throws NoSuchEntityException
     */
    public function resolve(): StockProviderInterface
    {
        $sortedResolvers = $this->sort($this->resolvers);
        $provider = null;
        foreach ($sortedResolvers as $resolver) {
            /** @var StockResolverInterface $resolverInstance */
            $resolverInstance = $resolver['objectInstance'] ?? null;
            if (!$resolverInstance) {
                continue;
            }

            try {
                $provider = $resolverInstance->resolve();
            } catch (NoSuchEntityException $exception) {
                // do nothing
            }

            if ($provider) {
                break;
            }
        }

        if (!$provider) {
            throw new NoSuchEntityException(__('No available stock provider for feed generation'));
        }

        return $provider;
    }

    /**
     * Sorting modifiers according to sort order
     *
     * @param array $data
     * @return array
     */
    private function sort(array $data)
    {
        usort($data, function (array $a, array $b) {
            return $this->getSortOrder($a) <=> $this->getSortOrder($b);
        });

        return $data;
    }

    /**
     * Retrieve sort order from array
     *
     * @param array $variable
     * @return int
     */
    private function getSortOrder(array $variable)
    {
        return !empty($variable['sortOrder']) ? $variable['sortOrder'] : 0;
    }
}
