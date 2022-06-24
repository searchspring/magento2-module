<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\ResourceModel\Task\Error;

use Magento\Framework\App\ResourceConnection;
use SearchSpring\Feed\Api\Data\TaskErrorInterface;
use SearchSpring\Feed\Api\Data\TaskErrorInterfaceFactory;
use SearchSpring\Feed\Model\ResourceModel\Task;

class LoadErrors
{
    /**
     * @var TaskErrorInterfaceFactory
     */
    private $taskErrorFactory;
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * LoadErrors constructor.
     * @param TaskErrorInterfaceFactory $taskErrorFactory
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        TaskErrorInterfaceFactory $taskErrorFactory,
        ResourceConnection $resourceConnection
    ) {
        $this->taskErrorFactory = $taskErrorFactory;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param array $taskIds
     * @return array
     * @throws \Exception
     */
    public function execute(array $taskIds) : array
    {
        if (empty($taskIds)) {
            throw new \Exception();
        }
        $taskIds = array_map('intval', $taskIds);
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()->from(Task::ERROR_TABLE)
            ->where('task_id in (?)', $taskIds);
        $errors = $connection->fetchAll($select);
        $result = [];
        foreach ($errors as $error) {
            $errorData = [
                TaskErrorInterface::CODE => (int) $error[TaskErrorInterface::CODE],
                TaskErrorInterface::MESSAGE => $error[TaskErrorInterface::MESSAGE]
            ];
            $result[(int) $error['task_id']] = $this->taskErrorFactory->create(['data' => $errorData]);
        }

        return $result;
    }
}
