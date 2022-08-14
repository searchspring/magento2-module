<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model;

use Exception;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use SearchSpring\Feed\Api\Data\TaskErrorInterface;
use SearchSpring\Feed\Api\Data\TaskErrorInterfaceFactory;
use SearchSpring\Feed\Api\Data\TaskInterface;
use SearchSpring\Feed\Api\ExecuteTaskInterface;
use SearchSpring\Feed\Api\MetadataInterface;
use SearchSpring\Feed\Api\TaskRepositoryInterface;
use SearchSpring\Feed\Exception\GenericException;
use SearchSpring\Feed\Model\Task\ExecutorPool;

class ExecuteTask implements ExecuteTaskInterface
{
    /**
     * @var ExecutorPool
     */
    private $executorPool;
    /**
     * @var TaskRepositoryInterface
     */
    private $taskRepository;
    /**
     * @var DateTime
     */
    private $dateTime;
    /**
     * @var TaskErrorInterfaceFactory
     */
    private $taskErrorFactory;

    /**
     * ExecuteTask constructor.
     * @param ExecutorPool $executorPool
     * @param TaskRepositoryInterface $taskRepository
     * @param DateTime $dateTime
     * @param TaskErrorInterfaceFactory $taskErrorFactory
     */
    public function __construct(
        ExecutorPool $executorPool,
        TaskRepositoryInterface $taskRepository,
        DateTime $dateTime,
        TaskErrorInterfaceFactory $taskErrorFactory
    ) {
        $this->executorPool = $executorPool;
        $this->taskRepository = $taskRepository;
        $this->dateTime = $dateTime;
        $this->taskErrorFactory = $taskErrorFactory;
    }

    /**
     * @param TaskInterface $task
     * @return mixed
     * @throws CouldNotSaveException
     */
    public function execute(TaskInterface $task)
    {
        $executor = $this->executorPool->get($task->getType());
        $time = $this->dateTime->gmtDate();
        $task->setStartedAt($time)
            ->setStatus(MetadataInterface::TASK_STATUS_PROCESSING);
        $this->taskRepository->save($task);
        $result = null;
        try {
            $result = $executor->execute($task);
            $task->setStatus(MetadataInterface::TASK_STATUS_SUCCESS);
        } catch (Exception $exception) {
            /** @var TaskErrorInterface $error */
            $error = $this->taskErrorFactory->create();
            $code = $exception instanceof GenericException ? $exception->getCode() : GenericException::CODE;
            $error->setMessage($exception->getMessage())
                ->setCode($code);
            $task->setStatus(MetadataInterface::TASK_STATUS_ERROR)
                ->setError($error);
        }
        $time = $this->dateTime->gmtDate();
        $task->setEndedAt($time);
        $this->taskRepository->save($task);
        return $result;
    }
}
