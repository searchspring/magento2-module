<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\Storage;

class WriterPool
{
    /**
     * @var array
     */
    private $writers;

    /**
     * WriterPool constructor.
     * @param array $writers
     */
    public function __construct(
        array $writers = []
    ) {
        $this->writers = $writers;
    }

    /**
     * @param string $format
     * @return WriterInterface|null
     */
    public function get(string $format) : ?WriterInterface
    {
        return $this->writers[$format] ?? null;
    }
}
