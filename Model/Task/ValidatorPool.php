<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Task;

class ValidatorPool
{
    /**
     * @var array
     */
    private $validators;

    /**
     * ValidatorPool constructor.
     * @param array $validators
     */
    public function __construct(
        array $validators = []
    ) {
        $this->validators = $validators;
    }

    /**
     * @param string $type
     * @return ValidatorInterface|null
     */
    public function get(string $type) : ?ValidatorInterface
    {
        return $this->validators[$type] ?? null;
    }
}
