<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Task;

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
     */
    public function get(string $type) : ResultFetcherInterface
    {
        if (!isset($this->fetchers[$type])) {
            throw new \Exception();
        }

        return $this->fetchers[$type];
    }
}
