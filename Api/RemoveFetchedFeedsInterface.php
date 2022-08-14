<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Api;

interface RemoveFetchedFeedsInterface
{
    /**
     * return removed feed count
     * @return int
     */
    public function execute() : int;
}
