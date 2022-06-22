<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\ResourceModel\Task\Error;

use Magento\Framework\App\ResourceConnection;
use SearchSpring\Feed\Model\ResourceModel\Task;

class DeleteErrors
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * DeleteErrors constructor.
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param array $taskIds
     */
    public function execute(array $taskIds) : void
    {
        $taskIds = array_map('intval', $taskIds);
        $this->resourceConnection->getConnection()->delete(Task::ERROR_TABLE, ['task_id IN (?)' => $taskIds]);
    }
}
