<?php
/**
 * Copyright (C) 2023 Searchspring <https://searchspring.com>
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace SearchSpring\Feed\Model;

use Exception;
use Magento\Framework\Exception\CouldNotSaveException;
use SearchSpring\Feed\Api\CreateTaskInterface;
use SearchSpring\Feed\Api\Data\TaskInterface;
use SearchSpring\Feed\Api\Data\TaskInterfaceFactory;
use SearchSpring\Feed\Api\MetadataInterface;
use SearchSpring\Feed\Api\TaskRepositoryInterface;
use SearchSpring\Feed\Exception\UniqueTaskException;
use SearchSpring\Feed\Exception\ValidationException;
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
     * @throws CouldNotSaveException
     * @throws ValidationException
     * @throws Exception
     */
    public function execute(string $type, $payload): TaskInterface
    {
        if (!is_array($payload)) {
            throw new Exception((string) __('$payload must be array'));
        }

        if (!$this->typeList->exist($type)) {
            $availableTaskTypes = implode(', ', $this->typeList->getAll());
            $message = [
                (string) __('Invalid task type \'%1\', available task types: %2', $type, $availableTaskTypes)
            ];
            throw new ValidationException($message);
        }

        $validator = $this->validatorPool->get($type);
        if ($validator) {
            $validationResult = $validator->validate($payload);
            if (!$validationResult->isValid()) {
                $errors = $validationResult->getErrors();
                throw new ValidationException($errors);
            }
        }

        $uniqueChecker = $this->uniqueCheckerPool->get($type);
        if ($uniqueChecker && !$uniqueChecker->check($payload)) {
            throw new UniqueTaskException();
        }

        /** @var TaskInterface $task */
        $task = $this->taskFactory->create();
        $task->setType($type)
            ->setPayload($payload)
            ->setStatus(MetadataInterface::TASK_STATUS_PENDING);

        return $this->taskRepository->save($task);
    }
}
