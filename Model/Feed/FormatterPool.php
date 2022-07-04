<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed;

class FormatterPool
{
    /**
     * @var array
     */
    private $formatters;

    /**
     * FormatterPool constructor.
     * @param array $formatters
     */
    public function __construct(
        array $formatters = []
    ) {
        $this->formatters = $formatters;
    }

    /**
     * @param string $type
     * @return FormatterInterface|null
     */
    public function get(string $type) : ?FormatterInterface
    {
        return $this->formatters[$type] ?? null;
    }
}
