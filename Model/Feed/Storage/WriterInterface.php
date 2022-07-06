<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\Storage;

use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;

interface WriterInterface
{
    /**
     * @param string $path
     * @param string $directory
     * @param array $data
     */
    public function write(
        string $path,
        string $directory,
        array $data
    ) : void;
}
