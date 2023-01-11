<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\Storage;

class FilePool
{
    /**
     * @var array
     */
    private $files;

    /**
     * FilePool constructor.
     * @param array $files
     */
    public function __construct(
        array $files = []
    ) {
        $this->files = $files;
    }

    /**
     * @param string $format
     * @return string|null
     */
    public function get(string $format) : ?string
    {
        return $this->files[$format] ?? null;
    }
}
