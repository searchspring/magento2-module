<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\ResourceModel\Task;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\Collection as AbstractCollection;
use SearchSpring\Feed\Model\ResourceModel\Task as TaskResource;
use SearchSpring\Feed\Model\Task;

class Collection extends AbstractCollection
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init(Task::class, TaskResource::class);
    }
}
