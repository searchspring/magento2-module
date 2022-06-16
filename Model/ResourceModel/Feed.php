<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;
use SearchSpring\Feed\Api\Data\FeedInterface;

class Feed extends AbstractDb
{
    const TABLE = 'searchspring_feed';
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE, FeedInterface::ENTITY_ID);
    }
}
