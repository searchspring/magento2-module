<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Api;

use Exception;
use Magento\Framework\Exception\CouldNotSaveException;
use SearchSpring\Feed\Api\Data\TaskInterface;
use SearchSpring\Feed\Exception\UniqueTaskException;
use SearchSpring\Feed\Exception\ValidationException;

interface CreateTaskInterface
{
    /**
     * @param string $type
     * @param mixed $payload
     * @return TaskInterface
     * @throws CouldNotSaveException
     * @throws ValidationException
     * @throws UniqueTaskException
     * @throws Exception
     */
    public function execute(string $type, $payload) : TaskInterface;
}
