<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Task;

interface UniqueCheckerInterface
{
    /**
     * @param array $payload
     * @return bool
     */
    public function check(array $payload) : bool;
}
