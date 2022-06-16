<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\ResourceModel\Feed;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\Collection as AbstractCollection;
use SearchSpring\Feed\Model\Feed;
use SearchSpring\Feed\Model\ResourceModel\Feed as FeedResource;

class Collection extends AbstractCollection
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init(Feed::class, FeedResource::class);
    }
}
