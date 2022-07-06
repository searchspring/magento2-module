<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed;

interface CollectionConfigInterface
{
    /**
     * @return int
     */
    public function getPageSize() : int;
}
