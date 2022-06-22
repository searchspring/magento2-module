<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\ResourceModel\Task\Error;

use Magento\Framework\App\ResourceConnection;
use SearchSpring\Feed\Api\Data\TaskErrorInterface;
use SearchSpring\Feed\Model\ResourceModel\Task;

class SaveError
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var DeleteErrors
     */
    private $deleteErrors;

    /**
     * SaveError constructor.
     * @param ResourceConnection $resourceConnection
     * @param DeleteErrors $deleteErrors
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        DeleteErrors $deleteErrors
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->deleteErrors = $deleteErrors;
    }

    /**
     * @param int $taskId
     * @param TaskErrorInterface $error
     * @throws \Exception
     */
    public function execute(int $taskId, TaskErrorInterface $error) : void
    {
        $connection = $this->resourceConnection->getConnection();
        if (!$error->getCode() || $error->getMessage()) {
            throw new \Exception();
        }

        $connection->beginTransaction();
        try {
            $this->deleteErrors->execute([$taskId]);
            $data = [
                'task_id' => $taskId,
                'code' => $error->getCode(),
                'message' => $error->getMessage()
            ];
            $connection->insert(Task::ERROR_TABLE, $data);
            $connection->commit();
        } catch (\Exception $exception) {
            $connection->rollBack();
            throw $exception;
        }
    }
}
