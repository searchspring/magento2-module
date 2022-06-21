<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Task;

class UniqueCheckerPool
{
    /**
     * @var array
     */
    private $checkers;

    /**
     * UniqueCheckerPool constructor.
     * @param array $checkers
     */
    public function __construct(
        array $checkers = []
    ) {
        $this->checkers = $checkers;
    }

    /**
     * @param string $code
     * @return UniqueCheckerInterface|null
     */
    public function get(string $code) : ?UniqueCheckerInterface
    {
        return $this->checkers[$code] ?? null;
    }
}
