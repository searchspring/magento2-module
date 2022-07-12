<?php

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
