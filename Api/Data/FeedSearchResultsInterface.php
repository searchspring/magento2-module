<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface FeedSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return FeedInterface[]
     */
    public function getItems();

    /**
     * @param FeedInterface[] $items
     * @return FeedSearchResultsInterface
     */
    public function setItems(array $items);
}
