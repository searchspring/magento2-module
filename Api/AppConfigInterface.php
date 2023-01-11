<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Api;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\RuntimeException;

interface AppConfigInterface
{
    /**
     * @param string $code
     * @return mixed
     * @throws FileSystemException
     * @throws RuntimeException
     */
    public function getValue(string $code);

    /**
     * @return bool
     * @throws FileSystemException
     * @throws RuntimeException
     */
    public function isDebug() : bool;
}
