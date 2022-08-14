<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Task;

use Exception;

class ResultFetcherPool
{
    /**
     * @var array
     */
    private $fetchers;

    /**
     * ResultFetcherPool constructor.
     * @param array $fetchers
     */
    public function __construct(
        array $fetchers = []
    ) {
        $this->fetchers = $fetchers;
    }

    /**
     * @param string $type
     * @return ResultFetcherInterface
     * @throws Exception
     */
    public function get(string $type) : ResultFetcherInterface
    {
        if (!isset($this->fetchers[$type])) {
            throw new Exception((string) __('No task result fetcher for type %1', $type));
        }

        return $this->fetchers[$type];
    }
}
