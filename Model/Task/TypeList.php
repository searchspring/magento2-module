<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Task;

class TypeList
{
    /**
     * @var array
     */
    private $types;

    /**
     * TypeList constructor.
     * @param array $types
     */
    public function __construct(
        array $types = []
    ) {
        $this->types = $types;
    }

    /**
     * @return array
     */
    public function getAll() : array
    {
        return $this->types;
    }

    /**
     * @param string $code
     * @return bool
     */
    public function exist(string $code) : bool
    {
        return isset($this->types[$code]);
    }
}
