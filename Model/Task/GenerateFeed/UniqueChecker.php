<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Task\GenerateFeed;

use SearchSpring\Feed\Model\Task\UniqueCheckerInterface;

class UniqueChecker implements UniqueCheckerInterface
{

    /**
     * @param array $payload
     * @return bool
     */
    public function check(array $payload): bool
    {
        // TODO: Implement check() method.
    }
}
