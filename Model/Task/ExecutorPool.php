<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Task;

class ExecutorPool
{
    /**
     * @var array
     */
    private $executors;

    /**
     * ExecutorPool constructor.
     * @param array $executors
     */
    public function __construct(
        array $executors = []
    ) {
        $this->executors = $executors;
    }

    /**
     * @param string $code
     * @return ExecutorInterface
     * @throws \Exception
     */
    public function get(string $code) : ExecutorInterface
    {
        if (!isset($this->executors[$code])) {
            throw new \Exception((string) __('No task executor for type %1', $code));
        }

        return $this->executors[$code];
    }
}
