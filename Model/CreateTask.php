<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model;

use SearchSpring\Feed\Api\CreateTaskInterface;
use SearchSpring\Feed\Api\Data\TaskInterface;
use SearchSpring\Feed\Api\Data\TaskInterfaceFactory;
use SearchSpring\Feed\Api\MetadataInterface;
use SearchSpring\Feed\Api\TaskRepositoryInterface;
use SearchSpring\Feed\Model\Task\TypeList;
use SearchSpring\Feed\Model\Task\UniqueCheckerPool;
use SearchSpring\Feed\Model\Task\ValidatorPool;

class CreateTask implements CreateTaskInterface
{
    /**
     * @var TaskRepositoryInterface
     */
    private $taskRepository;
    /**
     * @var TaskInterfaceFactory
     */
    private $taskFactory;
    /**
     * @var ValidatorPool
     */
    private $validatorPool;
    /**
     * @var TypeList
     */
    private $typeList;
    /**
     * @var UniqueCheckerPool
     */
    private $uniqueCheckerPool;

    /**
     * CreateTask constructor.
     * @param TaskRepositoryInterface $taskRepository
     * @param TaskInterfaceFactory $taskFactory
     * @param ValidatorPool $validatorPool
     * @param TypeList $typeList
     * @param UniqueCheckerPool $uniqueCheckerPool
     */
    public function __construct(
        TaskRepositoryInterface $taskRepository,
        TaskInterfaceFactory $taskFactory,
        ValidatorPool $validatorPool,
        TypeList $typeList,
        UniqueCheckerPool $uniqueCheckerPool
    ) {
        $this->taskRepository = $taskRepository;
        $this->taskFactory = $taskFactory;
        $this->validatorPool = $validatorPool;
        $this->typeList = $typeList;
        $this->uniqueCheckerPool = $uniqueCheckerPool;
    }

    /**
     * @param string $type
     * @param array $payload
     * @return TaskInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function execute(string $type, $payload): TaskInterface
    {
        if (!is_array($payload)) {
            throw new \Exception();
        }

        if (!$this->typeList->exist($type)) {
            throw new \Exception();
        }

        $validator = $this->validatorPool->get($type);
        if ($validator) {
            $validationResult = $validator->validate($payload);
            if (!$validationResult->isValid()) {
                throw new \Exception();
            }
        }

        $uniqueChecker = $this->uniqueCheckerPool->get($type);
        if ($uniqueChecker && !$uniqueChecker->check($payload)) {
            throw new \Exception();
        }

        /** @var TaskInterface $task */
        $task = $this->taskFactory->create();
        $task->setType($type)
            ->setPayload($payload)
            ->setStatus(MetadataInterface::TASK_STATUS_PENDING);

        return $this->taskRepository->save($task);
    }
}
