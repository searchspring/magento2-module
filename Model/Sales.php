<?php

namespace SearchSpring\Feed\Model;

use SearchSpring\Feed\Api\Data\SalesInterface;

class Sales implements SalesInterface
{
    private $sales;

    public function __construct()
    {
    }

    /**
     * @return SalesDataInterface[]
     */
    public function getSales(): array
    {
        return $this->sales;
    }

    /**
     * @param $value SalesDataInterface[]
     */
    public function setSales(array $value)
    {
        $this->sales = $value;
    }
}