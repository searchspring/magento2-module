<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed;

class SystemFieldsList
{
    /**
     * @var array
     */
    private $systemFields;

    /**
     * SystemFieldsList constructor.
     * @param array $systemFields
     */
    public function __construct(
        array $systemFields = []
    ) {
        $this->systemFields = $systemFields;
    }

    /**
     * @param string $field
     * @return SystemFieldsList
     */
    public function add(string $field) : self
    {
        if (!in_array($field, $this->systemFields)) {
            $this->systemFields[] = $field;
        }

        return $this;
    }
    /**
     * @return array
     */
    public function get() : array
    {
        return $this->systemFields;
    }

    /**
     * @param string $field
     * @return bool
     */
    public function isSystem(string $field) : bool
    {
        return in_array($field, $this->systemFields);
    }
}
