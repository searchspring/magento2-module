<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;
use SearchSpring\Feed\Api\Data\TaskInterface;

class Task extends AbstractDb
{
    const TABLE = 'searchspring_task';
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE, TaskInterface::ENTITY_ID);
    }
}
