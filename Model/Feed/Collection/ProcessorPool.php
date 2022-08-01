<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\Collection;

class ProcessorPool
{
    /**
     * @var array
     */
    private $processors;

    /**
     * ProcessorPool constructor.
     * @param array $processors
     */
    public function __construct(
        array $processors = []
    ) {
        $this->processors = $processors;
    }

    /**
     * @return ProcessCollectionInterface[]
     */
    public function getAll() : array
    {
        return $this->processors;
    }
}
