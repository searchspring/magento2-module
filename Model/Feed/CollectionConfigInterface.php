<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\RuntimeException;

interface CollectionConfigInterface
{
    /**
     * @return int
     * @throws FileSystemException
     * @throws RuntimeException
     */
    public function getPageSize() : int;
}
